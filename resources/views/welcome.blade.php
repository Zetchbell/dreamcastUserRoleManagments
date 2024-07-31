<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dreamcast User Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .card-header {
            background-color: #007bff;
            color: white;
        }
        label.error{
            color:red;
        }
    </style>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>


</head>
<body>
    <div class="container mt-5">
        <div class="col-md-2"></div>
        <div class="col-md-8">       
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">User Management Form</h4>
                </div>
                <div class="card-body">

                    <div id="formErrorDiv" class="text-danger"></div>

                    <form id="userManagementForm" method="post"  name="userManagementForm" enctype="multipart/form-data">
                        @csrf
                      
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" value="" name="name" id="name" class="form-control" placeholder="Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" value="" name="email" id="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="text" value="" name="phone" id="phone" class="form-control" placeholder="Phone" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Role <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option disabled value="" selected>--SELECT--</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="profile_image">Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                    </form>
                </div>
            </div>
            <h2 class="mb-4">User List</h2>
            <table id="userManagementTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th>Role Name</th>
                        <th>Profile Image</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table rows will be added dynamically -->
                </tbody>
            </table>
        </div> 
        <div class="col-md-2"></div>
    </div>


    <script>
    jQuery.validator.addMethod("valid_email", function(value, element, param) {
        return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
    },'');

    $.validator.addMethod("valid_name", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
    }, "Name must contain only letters and spaces");

    $.validator.addMethod("phoneNumber", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Please enter a valid phone number starting with 6, 7, 8, or 9.");


    jQuery.validator.addMethod("numericonly", function(value, element) {
    return this.optional(element) || /^[0-9]*$/.test(value);
    }, "numeric only please"); 

$(document).ready(function () {
            $('#userManagementForm').validate({
                rules: {
                    name:{
                        required: true,
                        valid_name:true
                    },
                    email: {
                        required: true,
                        email: true,
                        valid_email:true
                    },
                    phone: {
                        required: true,
                        numericonly:true,
                        minlength: 10,
                        maxlength: 10,
                        phoneNumber:true
                    },
                    description: {
                        required: true
                    },
                    role_id: {
                        required: true
                    }
                },
                messages: {
                    name:{
                        required: "Please enter your name",
                    },
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email address"
                    },
                    phone: {
                        required: "Please enter your phone number"
                    },
                    role_id: {
                        required: "Please choose a role"
                    }
                },
                submitHandler: function (form) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                   
                    $.ajax({
                        url: "{{ route('user.saveUserData') }}",
                        type: 'POST',
                        data: new FormData(form),
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#formErrorDiv').html('');
                           if(response.status == 1){
                            loadUsers();
                           }else{
                            alert(response.message);
                           }
                           
                        },
                        error: function(xhr) {
                            console.log('Error:', xhr);
                            $('#formErrorDiv').html('');
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                messages.forEach(function(message) {
                                    $('#formErrorDiv').append('<p class="alert alert-danger">' + message + '</p>');
                                });
                            });
                            $("body").scrollTop();
                        }
                    });

                }
            });

            function loadUsers() { 
                $.ajax({
                    url: "{{ route('getUserData') }}",
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (data) {                      
                        var tableBody = $('#userManagementTable tbody');
                        tableBody.empty();
                        $.each(data, function (index, user) {
                            tableBody.append('<tr>' +
                                '<td>' + user.name + '</td>' +
                                '<td>' + user.email + '</td>' +
                                '<td>' + user.phone + '</td>' +
                                '<td>' + user.description + '</td>' +
                                '<td>' + user.role + '</td>' +
                                '<td>' + user.image  + '</td>' +
                                '</tr>');
                        });
                    }
                });
            }
            loadUsers();
        });
    </script>
</body>
</html>

