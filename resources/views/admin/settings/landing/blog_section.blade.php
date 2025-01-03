<div class="page-title">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-inner">
                <div class="table-title mb-4">
                    <h3>{{__('Blog Section')}}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-area plr-65 profile-info-form">
    <form enctype="multipart/form-data" method="POST"
          action="{{route('adminLandingSettingSave')}}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="#">{{__('Blog Section Heading')}}</label>
                            <input type="text" class="form-control" name="blog_section_heading"
                                   @if(isset($adm_setting['blog_section_heading']))value="{{$adm_setting['blog_section_heading']}}" @endif>
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="#">{{__('Blog Section Description')}}</label>
                            <input type="text" class="form-control" name="blog_section_description"
                                   @if(isset($adm_setting['blog_section_description']))value="{{$adm_setting['blog_section_description']}}" @endif>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="#">{{__('Blog Section Banner Description')}}</label>
                            <input type="text" class="form-control" name="blog_section_banner_description"
                                   @if(isset($adm_setting['blog_section_banner_description']))value="{{$adm_setting['blog_section_banner_description']}}" @endif>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="#">{{__('Blog Section Banner Image')}}</label>
                            <div id="file-upload" class="section-width">
                                <input type="file" placeholder="0.00" name="blog_section_banner_image" value="" id="file" ref="file"
                                       class="dropify" @if(isset($adm_setting['blog_section_banner_image'])) data-default-file="{{asset(path_image().$adm_setting['blog_section_banner_image'])}}"@endif />
                            </div>
                        </div>
                        
                    </div> -->
                    <div class="col-lg-12">
                        <button class="button-primary theme-btn">{{__('Update')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
