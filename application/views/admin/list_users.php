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
        <a class="btn" href="<?php echo base_url().'main' ?>">Retour au site</a>
    </form>
  </div>
</nav>
<script type="text/javascript">
$(document).ready(function() {
    $('#user-table').DataTable();
} );
</script>
<a href="<?php echo base_url()."admin/add/"; ?>">Add user <i class="fa fa-plus-sign"></i></a>
<?php echo $flash_message; ?>
<table id="user-table" class="table table-bordered table-stripped">
    <thead>
        <th>Id</th>
        <th>E-mail</th>
        <th>First name</th>
        <th>Last name</th>
        <th>Role</th>
        <th>Last login</th>
        <th>Expires</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php
        foreach($users as $value){
            $expires = isset($value->expires) ? $value->expires : "Never";
            echo "<tr>";
            echo "<td>$value->id</td>";
            echo "<td>$value->email</td>";
            echo "<td>$value->first_name</td>";
            echo "<td>$value->last_name</td>";
            echo "<td>$value->role</td>";
            echo "<td>$value->last_login</td>";
            echo "<td>$expires</td>";
            echo "<td><a href=\"".base_url()."admin/delete_user/$value->id\" data-confirm=\"Are you sure you want to delete this user ?\">Delete</a>";
            echo "</tr>";
        }?>
    </tbody>
</table>