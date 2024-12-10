<ul class="d-flex activity-menu">
    <li class="viewuser">
        <a href="{{route('adminIeoEdit', encrypt($ieo->id))}}" title="{{__("Update")}}" class="btn btn-primary btn-sm">
            <i class="fa fa-pencil"></i>
        </a>
    </li>
    <li class="viewuser">
        <a href="#deleteIEO_{{($ieo->id)}}" data-toggle="modal" title="{{__("Delete")}}" class="btn btn-danger btn-sm">
            <i class="fa fa-trash"></i>
        </a>
        <div id="deleteIEO_{{($ieo->id)}}" class="modal fade delete" role="dialog">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header"><h6 class="modal-title">{{__('Delete')}}</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                    <div class="modal-body"><p>{{ __('Do you want to delete :name?', ['name' => $ieo->name]) }}</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{__("Close")}}</button>
                        <a class="btn btn-danger" href="{{route('adminIeoDelete', encrypt($ieo->id))}}">{{__('Confirm')}} </a>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
