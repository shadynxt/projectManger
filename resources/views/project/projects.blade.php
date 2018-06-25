@extends('layout')

@section('content')
<div class="row">
<div class="col-md-6">
<h1>LIST OF ACTIVE PROJECTS</h1>
</div>
<div class="col-md-6">
<form action="{{ route('project.search') }}" method="get" name="main_search_form" class="navbar-form">


        <div class="input-group">

            <input autocomplete="off" type="text" placeholder="Search Projects" class="form-control" name="project_search" id="project_search">
            <span class="input-group-btn">
            <button type="submit"  id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                        </button>
            </span>
        </div>


        </form>
      </div>
    </div>

<div class="new_project">
  <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add New Project</button>
</div>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Enter Project Title</h4>
      </div>
      <div class="modal-body">
        <form id="project_form" action="{{ route('project.store') }}" method="POST">
            {{ csrf_field() }}

        <div class="row">
            <div class="col-md-12">
            <div class="form-group">
              <input type="text" class="form-control" id="project" name="project">

              <div class="form-group">
            <label>Select start Date <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
            <div class='input-group date' id='datetimepicker2'>
                <input type='text' class="form-control" name="start_date">
                <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label>Select end Date <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
            <div class='input-group date' id='datetimepicker4'>
                <input type='text' class="form-control" name="end_date">
                <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <input class="btn btn-primary" type="submit" value="Submit" >
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>

        </form>
      </div>

    </div>

  </div>
</div>
<!--  END modal  -->



<div class="table-responsive">
<table class="table table-striped">
    <thead>
      <tr>
        <th>Project Name</th>
        <th>Project Tasks List</th>
        <th>Actions</th>
      </tr>
    </thead>

@if ( !$projects->isEmpty() ) 
    <tbody>
    @foreach ( $projects  as $project)
      <tr>
        <td>{{ $project->project_name }} </td>
        <td>
           <a href="{{ route('task.list', [ 'projectid' => $project->id ]) }}">List all tasks</a>
        </td>
        <td>
          <a class="btn btn-primary" href="{{ route('project.edit', [ 'id' => $project->id ]) }}"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>          
          <a class="btn btn-danger" href="{{ route('project.delete', [ 'id' => $project->id ]) }}" Onclick="return ConfirmDelete();"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>&nbsp;&nbsp;
        </td>

      </tr>

    @endforeach
    </tbody>
@else 
    <p><em>There are no tasks assigned yet</em></p>
@endif


</table>
</div>




@stop


@section('scripts')

    <script src="{{ asset('js/moment.js') }}"></script> 

    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>  

<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>

    <script>
        jQuery(document).ready(function() {

            jQuery(function() {
                
                jQuery('#datetimepicker2').datetimepicker( {
                    defaultDate:'now',  // defaults to today
                    format: 'YYYY-MM-DD hh:mm:ss',  // YEAR-MONTH-DAY hour:minute:seconds
                    minDate:new Date()  // Disable previous dates, minimum is todays date
                });
                
                jQuery('#datetimepicker4').datetimepicker( {
                    defaultDate:'now',  // defaults to today
                    format: 'YYYY-MM-DD hh:mm:ss',  // YEAR-MONTH-DAY hour:minute:seconds
                    minDate:new Date()  // Disable previous dates, minimum is todays date
                });
                
            });
        });
    </script>


    
<!-- TYPE AHEAD LIB -->
<script src="{{ asset('js/typeahead.min.js') }}"></script>

<script>

$(document).ready(function() {
    $('#project_search').on('keyup', function(e){
        if(e.which == 13){
            $('#main_search_form').submit();
        }
    });
    $.get("/main-search-autocomplete", function(data){
        $("#project_search").typeahead({
            "items": "all", // Number of Items
            "source": data,
            "autoSelect": false,
            displayText: function(item){
                console.log('returning item: ' + item.project_name ) ;
                return item.project_name;
            },

            updater: function(item) {
              // http://laratubedemo.test/admin/videos/search?video_search=Code+Geass+Op1
                window.location.href = '{{ route('project.search') }}?project_search=' + item.project_name.split(' ').join('+') ;
            }

        });
    },'json');
});

</script>



<script>
  console.log(" {{ url('/') }}" ) ;
  var editor_config = {
    //path_absolute : "/",
    path_absolute:"{{ url('/') }}/",
    selector: "textarea.my-editor",
    plugins: [
      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen",
      "insertdatetime media nonbreaking save table contextmenu directionality",
      "emoticons template paste textcolor colorpicker textpattern"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
    relative_urls: false,
    file_browser_callback : function(field_name, url, type, win) {
      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
      if (type == 'image') {
        cmsURL = cmsURL + "&type=Images";
      } else {
        cmsURL = cmsURL + "&type=Files";
      }

      tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : 'Filemanager',
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no"
      });
    },
    //  Add Bootstrap Image Responsive class for inserted images
    image_class_list: [
        {title: 'None', value: ''},
        {title: 'Bootstrap responsive image', value: 'img-responsive'},
    ]   


  };

  tinymce.init(editor_config);
</script>


@stop

<script>

function ConfirmDelete()
{
  var x = confirm("Are you sure? Deleting a Project will also delete all tasks associated with this project");
  if (x)
      return true;
  else
    return false;
}




</script>  
