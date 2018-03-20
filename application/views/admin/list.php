<nav class="navbar navbar-light bg-light justify-content-between">
  <a class="navbar-brand">Paracou-Ex Admin</a>
  <form class="form-inline">
      <a class="btn" href="<?php echo base_url().'main' ?>">Retour au site</a>
  </form>
</nav>
<a href="<?php echo base_url()."admin/add/"; ?>">Add user <i class="fa fa-plus-sign"></i></a>
<?php echo $flash_message; ?>
<table class="table table-bordered table-stripped">
    <head>
        <th>Id</th>
        <th>E-mail</th>
        <th>First name</th>
        <th>Last name</th>
        <th>Role</th>
        <th>Last login</th>
        <th>Action</th>
    </head>

<?php
foreach($users as $value){
    echo "<tr>";
    echo "<td>$value->id</td>";
    echo "<td>$value->email</td>";
    echo "<td>$value->first_name</td>";
    echo "<td>$value->last_name</td>";
    echo "<td>$value->role</td>";
    echo "<td>$value->last_login</td>";
    echo "<td><a href=\"".base_url()."admin/delete/$value->id\" data-confirm=\"Are you sure you want to delete this user ?\">Delete</a>";
    echo "</tr>";
}?>
</table>