<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand">Paracou-Ex Admin</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
            <a class="nav-link" href="<?php echo base_url().'admin/list_users' ?>">User list <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url().'admin/list_requests' ?>">Request list</a>
        </li>
    </ul>
    <form class="navbar-text form-inline">
        <a href="<?php echo base_url().'main' ?>">Back to homepage</a>
    </form>
  </div>
</nav>
<script type="text/javascript">
$(document).ready(function() {
    $('#user-table').DataTable();
} );
</script>
<a class="m-2 btn btn-primary" href="<?php echo base_url()."admin/add/"; ?>">Add user  <i class="fas fa-plus-circle"></i></a>
<table id="user-table" class="table table-bordered table-stripped">
    <thead>
        <th>Id</th>
        <th>E-mail</th>
        <th>Full name</th>
        <th>Role</th>
        <th>Last login</th>
        <th>Created</th>
        <th>Expires</th>
        <th>Request reference</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php
        foreach($users as $value){
            $expires = isset($value->expires) ? $value->expires : "Never";
            echo "<tr>";
            echo "<td>$value->id</td>";
            echo "<td>$value->email</td>";
            echo "<td>$value->first_name $value->last_name</td>";
            echo "<td>$value->role</td>";
            echo "<td>$value->last_login</td>";
            echo "<td>$value->created</td>";
            echo "<td>$expires</td>";
            echo "<td><a href=\"". base_url() ."admin/show_request/$value->request_id\">$value->request_id</a></td>";
            echo "<td><a class=\"btn-sm btn-danger\" href=\"".base_url()."admin/delete_user/$value->id\" data-confirm=\"Are you sure you want to delete this user and his associed request ?\">Delete <i class=\"fas fa-trash\"></i></a>";
            echo "</tr>";
        }?>
    </tbody>
</table>