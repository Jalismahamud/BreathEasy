@extends('backend.app', ['title' => 'Categories'])

@push('styles')
<link href="{{ asset('default/datatable.css') }}" rel="stylesheet" />
@endpush

@section('content')
<!--app-content open-->
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Categories</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Categories</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- CATEGORY LIST TABLE -->
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card product-sales-main">
                        <div class="card-header border-bottom">
                            <h3 class="card-title mb-0">Category List</h3>
                            <div class="card-options ms-auto">
                                @if(\App\Models\Category::count() < 4)
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">Add Category</button>
                                @else
                                    <button class="btn btn-primary btn-sm" disabled>Category Limit Reached</button>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table text-nowrap mb-0 table-bordered" id="datatable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END TABLE -->
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createCategoryForm" method="post" action="{{ route('admin.category.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" placeholder="Title" id="title" value="{{ old('title') }}">
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image" class="form-label">Image:</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="post" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="editTitle" class="form-label">Title:</label>
                        <input type="text" class="form-control" name="title" id="editTitle" placeholder="Title">
                    </div>

                    <div class="form-group">
                        <label for="editImage" class="form-label">Image:</label>
                        <input type="file" class="form-control" name="image" id="editImage">
                        <img id="currentImage" src=""  style="max-height: 100px; margin-top: 10px;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            }
        });

        $('#datatable').DataTable({
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('admin.category.index') }}",
            language: {
                processing: `<div class="text-center"><img src="{{ asset('default/loader.gif') }}" style="width:50px;"></div>`
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'image', name: 'image' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'dt-center' },
            ]
        });

        // Status Change
        window.showStatusChangeAlert = function (id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update the status?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = "{{ route('admin.category.status', ':id') }}".replace(':id', id);
                    $.post(url, function (resp) {
                        toastr.success(resp.message);
                        $('#datatable').DataTable().ajax.reload();
                    }).fail(function (err) {
                        toastr.error(err.responseJSON.message);
                    });
                }
            });
        };

        // Delete
        window.showDeleteConfirm = function (id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = "{{ route('admin.category.destroy', ':id') }}".replace(':id', id);
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function (resp) {
                            toastr.success(resp.message);
                            $('#datatable').DataTable().ajax.reload();
                        },
                        error: function (err) {
                            toastr.error(err.responseJSON.message);
                        }
                    });
                }
            });
        };

        // Edit
        window.goToEdit = function (id) {
            let url = "{{ route('admin.category.edit', ':id') }}".replace(':id', id);
            $.get(url, function (data) {
                $('#editCategoryForm').attr('action', "{{ route('admin.category.update', ':id') }}".replace(':id', id));
                $('#editTitle').val(data.title);
                $('#currentImage').attr('src', data.image_url);
                $('#editCategoryModal').modal('show');
            });
        };
    });
</script>
@endpush
