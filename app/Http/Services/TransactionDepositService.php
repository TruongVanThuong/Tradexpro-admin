<?php
namespace App\Http\Services;

use Exception;
use App\Model\Coin;
use App\Model\Wallet;
use App\Jobs\TransactionDeposit;
use App\Model\DepositeTransaction;
use App\Model\WalletAddressHistory;

class TransactionDepositService
{
    public function __construct(){}

    public function getNetworks(): array
    {
        $responseData = [];
        $networks = function_exists('selected_node_network')? selected_node_network(): [];
        foreach ($networks as $key => $value) {
            $responseData[] = [
                "id" => $key,
                "name" => $value,
            ];
        }
        return responseData(true, __("Networks get successfully"), $responseData);
    }
    
    public function getCoinNetwork($request): array
    {
        if(!      isset($request->network_id)) return responseData(false, __("No network found"));
        if(! is_numeric($request->network_id)) return responseData(false, __("Network is invalid"));

        $coins = Coin::where("network", $request->network_id)->get(["id", "name", "coin_type", "network as network_id"]);
        return responseData(true, __("Network coins get successfully"), $coins);
    }
    
    public function checkCoinTransactionAndDeposit($request): array
    {
        $coin = Coin::join('coin_settings','coin_settings.coin_id', '=', 'coins.id')
                ->where(['coins.id' => $request->coin_id])
                ->first();

        // return if coin not exist
        if(! $coin) return responseData(false, __("Coin not found"));

        $erc20Api = new ERC20TokenApi($coin);
        $getTransaction = $erc20Api->getTransactionData([
            'transaction_hash' => $request->trx_id,
            'contract_address' => $coin->contract_address
        ]);

        $data = [];
        if(isset($getTransaction["success"]) && $getTransaction["success"]){
            $transactionData = $getTransaction['data'];
            $data = [
                'coin_type' => $coin->coin_type,
                'txId' => $transactionData->txID,
                'confirmations' => 1,
                'amount' => $transactionData->amount,
                'address' => $transactionData->toAddress,
                'from_address' => $transactionData->fromAddress
            ];

            $responseData = $data;
            $responseData["network"] = selected_node_network($coin->network);

            $checkAddress = WalletAddressHistory::where(['address' => $data['address'], 'coin_type' => $data['coin_type']])->first();
            if ($checkAddress) {
                $wallet = Wallet::find($checkAddress->wallet_id);
                if ($wallet) {
                    if(!$deposit = DepositeTransaction::where("transaction_id", $data['txId'])->first()){
                        TransactionDeposit::dispatch($data)->onQueue("deposit");
                        return responseData(true, __("Transaction details found, System will adjust deposit soon"), $responseData);
                    }
                    return responseData(true, __("This transaction already deposited in our system"), $responseData);
                }
            }
            return responseData(true, __("Transaction details found but To address not match in system"), $responseData);
        } else if(
            !$getTransaction["success"] && isset($getTransaction["data"]) 
            && isset($getTransaction["data"]->msg_for_user) 
            && $getTransaction["data"]->msg_for_user
        ) {
            return responseData(false, $getTransaction["message"]);
        }

        return responseData(false, __("Invalid transaction"));
    }

    public function checkAddressAndDeposit($data)
    {
        try {
            storeException('checkAddressAndDeposit', json_encode($data));
            $checkAddress = WalletAddressHistory::where(['address' => $data['address'], 'coin_type' => $data['coin_type']])->first();
            if ($checkAddress) {
                $wallet = Wallet::find($checkAddress->wallet_id);
                if ($wallet) {
                    if(!$deposit = DepositeTransaction::where("transaction_id", $data['txId'])->first()){

                        storeException('checkAddressAndDeposit wallet ', json_encode($wallet));
                        $deposit = DepositeTransaction::create($this->depositData($data,$wallet));
                        storeException('checkAddressAndDeposit created ', json_encode($deposit));
                        storeException('checkAddressAndDeposit wallet balance before ', $wallet->balance);
                        $wallet->increment('balance',$data['amount']);
                        storeException('checkAddressAndDeposit wallet balance increment ', $wallet->balance);
                        storeException('checkAddressAndDeposit', ' wallet deposit successful');
                        $response = responseData(false,__('Wallet deposited successfully'));
                    }else{
                        storeException('checkAddressAndDeposit', ' already deposited');
                        $response = responseData(false,__('already deposited'));
                    }
                } else {
                    storeException('checkAddressAndDeposit', ' wallet not found');
                    $response = responseData(false,__('wallet not found'));
                }
            } else {
                storeException('checkAddressAndDeposit', $data['address'].' this address not found in db ');
                $response = responseData(false,__('This address not found in db the address is ').$data['address']);
            }
        } catch (Exception $e) {
            storeException('checkAddressAndDeposit', $e->getMessage());
            $response = responseData(false,$e->getMessage());
        }
        return $response;
    }
// deposit data
    public function depositData($data,$wallet)
    {
        return [
            'address' => $data['address'],
            'from_address' => isset($data['from_address']) ? $data['from_address'] : "",
            'receiver_wallet_id' => $wallet->id,
            'address_type' => ADDRESS_TYPE_EXTERNAL,
            'coin_type' => $wallet->coin_type,
            'amount' => $data['amount'],
            'transaction_id' => $data['txId'],
            'status' => STATUS_SUCCESS,
            'confirmations' => $data['confirmations']
        ];
    }
}