<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
        }

        .card-profile {
            border: none;
            border-radius: 1rem;
            padding: 2.5rem;
            background-color: #fff;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1);
        }

        .profile-icon {
            font-size: 4rem;
            color: #0d6efd;
            /* background-color: #e7f1ff; */
            padding: 1rem;
            border-radius: 50%;
            display: inline-block;
        }

        .user-info {
            margin-top: 1rem;
        }

        .user-info h2 {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .user-info p {
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .btn-group-custom {
            display: flex;
            gap: 1rem;
        }

        .modal-content {
            border-radius: 0.75rem;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-6">
            <div class="card-profile text-center">
                <span class="profile-icon"><i class="bi bi-person-circle"></i></span>
                <div class="user-info">
                    <h2>Welcome, {{ $user->name }}!</h2>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="btn-group-custom justify-content-center mt-4">
                    <form action="{{ route('app.user.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">Logout</button>
                    </form>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to delete your account? This action cannot be undone.</p>
                    <form action="{{ route('app.user.delete.account', ['user' => $user]) }}" method="POST">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="confirm" value="DELETE">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
