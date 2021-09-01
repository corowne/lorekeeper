@extends('admin.layout')

@section('admin-title') File Manager @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Files' => 'admin/files'] + ($folder ? [$folder => 'admin/files/'.$folder] : [])) !!}

<h1>File Manager / {!! $folder ? $folder.' <a href="'.url('admin/files/').'" class="btn btn-success float-right">Back to Root</a>' : 'Root' !!}</h1>

<p>This manager allows you to upload files onto your server and create folders up to one level deep. Note that a folder containing files cannot be renamed or deleted.</p>


@if(!$folder)
    <div class="text-right mb-3"><a class="btn btn-outline-primary" id="createFolder" href="#"><i class="fas fa-plus"></i> Create New Folder</a></div>
    <div class="row mb-3">
        @foreach($folders as $f)
            <div class="col-md-4 col-xs-4 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ url('admin/files/'.basename($f)) }}"><i class="fas fa-folder"></i> {{ basename($f) }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@elseif(!count($files))
    <div class="text-right mb-3">
        <a class="btn btn-outline-primary" id="renameFolder" href="#">Rename Folder</a>
        <a class="btn btn-outline-danger" id="deleteFolder" href="#">Delete Folder</a>
    </div>
@endif

<div class="text-right mb-3"><a href="#" class="btn btn-outline-primary" id="uploadButton"><i class="fas fa-plus"></i> Upload File</a></div>
<table class="table table-sm">
    <thead>
        <tr>
            <th>Files</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $file)
            <tr>
                <td>
                    <a href="{{ asset('files/'.($folder ? $folder.'/' : '').$file) }}">{{ $file }}</a>
                </td>
                <td class="text-right">
                    <a href="#" class="btn btn-outline-primary btn-sm move-file" data-name="{{ $file }}" data-folder="{{ $folder }}">Move</a>
                    <a href="#" class="btn btn-outline-primary btn-sm rename-file" data-name="{{ $file }}" data-folder="{{ $folder }}">Rename</a>
                    <a href="#" class="btn btn-outline-danger btn-sm delete-file" data-name="{{ $file }}" data-folder="{{ $folder }}">Delete</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($folder && !count($files))
    <div class="modal fade" id="editFolderModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0" id="editFolderModalTitle"></span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/files/folder/rename', 'id' => 'renameFolderForm', 'class' => 'folder-form']) !!}
                        <p>This will rename the folder. Folders containing files cannot be renamed. Use alphanumeric characters and dashes/underscores only.</p>
                        <div class="form-group">
                            {!! Form::label('name', 'New Name') !!}
                            {!! Form::text('name', $folder, ['class' => 'form-control', 'id' => 'editFolderName']) !!}
                        </div>
                        <div class="text-right">
                            {!! Form::submit('Rename', ['class' => 'btn btn-primary']) !!}
                        </div>
                        {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['url' => 'admin/files/folder/delete', 'id' => 'deleteFolderForm', 'class' => 'folder-form']) !!}
                        <p>This will permanently delete <strong>{{ $folder }}</strong>. Are you sure?</p>
                        <div class="text-right">
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        </div>
                        {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@elseif(!$folder)
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Create Folder</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => 'admin/files/folder/create']) !!}
                        <p>This will create a new folder in the root folder. Use alphanumeric characters and dashes/underscores only.</p>
                        <div class="form-group">
                            {!! Form::label('name', 'Folder Name') !!}
                            {!! Form::text('name', '', ['class' => 'form-control']) !!}
                        </div>
                        <div class="text-right">
                            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h5 mb-0" id="editModalTitle"></span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Move a file --}}
                {!! Form::open(['url' => 'admin/files/move', 'id' => 'moveForm', 'class' => 'file-form']) !!}
                    <p>This will move the file. If a file exists in the destination folder with the same name, it will be overwritten.</p>
                    <div class="form-group">
                        {!! Form::label('folder', 'Destination Folder') !!}
                        <?php 
                            $folderSelection = ['root' => 'Root'];
                            foreach($folders as $f) {
                                $folderSelection[basename($f)] = basename($f);
                            }
                        ?>
                        {!! Form::select('destination', $folderSelection, null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="text-right">
                        {!! Form::submit('Move', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::hidden('filename', '', ['class' => 'edit-filename']) !!}
                    {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                {!! Form::close() !!}
                
                {{-- Rename a file --}}
                {!! Form::open(['url' => 'admin/files/rename', 'id' => 'renameForm', 'class' => 'file-form']) !!}
                    <p>This will rename the file. If a file exists in the same folder with the same name, it will be overwritten.</p>
                    <p>Use alphanumeric characters and dashes/underscores only. Include the file extension as well - you can change the file extension, but this is not recommended.</p>
                    <div class="form-group">
                        {!! Form::label('name', 'New Name') !!}
                        {!! Form::text('name', '', ['class' => 'form-control', 'id' => 'editFileName']) !!}
                    </div>
                    <div class="text-right">
                        {!! Form::submit('Rename', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::hidden('filename', '', ['class' => 'edit-filename']) !!}
                    {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                {!! Form::close() !!}
                
                {{-- Delete a file --}}
                {!! Form::open(['url' => 'admin/files/delete', 'id' => 'deleteForm', 'class' => 'file-form']) !!}
                    <p>This will permanently delete <strong id="deleteFilename"></strong>. Are you sure?</p>
                    <div class="text-right">
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    </div>
                    {!! Form::hidden('filename', '', ['class' => 'edit-filename']) !!}
                    {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                {!! Form::close() !!}
                
                {{-- Upload a file --}}
                {!! Form::open(['url' => 'admin/files/upload', 'id' => 'uploadForm', 'class' => 'file-form', 'files' => true]) !!}
                    <p>Select a file to upload. (Maximum size {{ min(ini_get("upload_max_filesize"), ini_get("post_max_size")) }}B.)</p>
                    <div id="fileList">
                        <div class="d-flex mb-2">
                            {!! Form::file('files[]', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="text-right">
                        {!! Form::submit('Upload', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::hidden('folder', $folder, ['class' => 'edit-folder']) !!}
                    <div class="btn btn-primary" id="add-file">
                        Add File
                    </div>
                {!! Form::close() !!}
                <div class="file-row hide mb-2">
                    {!! Form::file('files[]', ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        //////////
        $('#add-file').on('click', function(e) {
            e.preventDefault();
            addFileRow();
        });
        function addFileRow() {
            var $clone = $('.file-row').clone();
            $('#fileList').append($clone);
            $clone.removeClass('hide file-row');
            $clone.addClass('d-flex');
        }

        /////////
        $('#createFolder').on('click', function(e) {
            e.preventDefault();
            $('#createModal').modal('show');
        });
        $('.move-file').on('click', function(e) {
            e.preventDefault();
            $('#editFileName').val($(this).data('name'));
            
            $('#moveForm').find('.edit-filename').val($(this).data('name'));
            $('#editModalTitle').html('Move File');
            $('.file-form').addClass('hide');
            $('#moveForm').removeClass('hide');

            $('#editModal').modal('show');
        });
        $('.rename-file').on('click', function(e) {
            e.preventDefault();
            $('#editFileName').val($(this).data('name'));
            
            $('#renameForm').find('.edit-filename').val($(this).data('name'));
            $('#editModalTitle').html('Rename File');
            $('.file-form').addClass('hide');
            $('#renameForm').removeClass('hide');

            $('#editModal').modal('show');
        });
        $('.delete-file').on('click', function(e) {
            e.preventDefault();
            $('#deleteForm').find('.edit-filename').val($(this).data('name'));
            $('#deleteFilename').html($(this).data('name'));
            $('#editModalTitle').html('Delete File');
            $('.file-form').addClass('hide');
            $('#deleteForm').removeClass('hide');

            $('#editModal').modal('show');
        });
        $('#uploadButton').on('click', function(e) {
            e.preventDefault();
            $('#editModalTitle').html('Upload File');
            $('.file-form').addClass('hide');
            $('#uploadForm').removeClass('hide');

            $('#editModal').modal('show');
        });
        $('#renameFolder').on('click', function(e) {
            e.preventDefault();
            $('#editFolderModalTitle').html('Rename Folder');
            $('.folder-form').addClass('hide');
            $('#renameFolderForm').removeClass('hide');

            $('#editFolderModal').modal('show');
        });
        $('#deleteFolder').on('click', function(e) {
            e.preventDefault();
            $('#editFolderModalTitle').html('Delete Folder');
            $('.folder-form').addClass('hide');
            $('#deleteFolderForm').removeClass('hide');

            $('#editFolderModal').modal('show');
        });
    });
</script>
@endsection