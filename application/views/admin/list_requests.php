<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand">Paracou-Ex Admin</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url().'admin/list_users' ?>">User list</a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="<?php echo base_url().'admin/list_requests' ?>">Request list<span class="sr-only">(current)</span></a>
        </li>
    </ul>
    <form class="navbar-text form-inline">
        <a href="<?php echo base_url().'main' ?>">Back to homepage</a>
    </form>
  </div>
</nav>
<script type="text/javascript">
$(document).ready(function() {
    $('#request-table').DataTable();
} );
</script>
<form method="get">
    <button class="m-2 btn btn-primary" type="submit" name="csv" />
    Export to CSV  <i class="fas fa-share"></i>
    </button>
</form>
<table id="request-table" class="table table-bordered table-stripped">
    <thead>
        <th>Id</th>
        <th>E-mail</th>
        <th>Full name</th>
        <th>Affiliation</th>
        <th>Title Research</th>
        <th>Timeline</th>
        <th>Accepted</th>
        <th>Actions</th>
    </thead>
    <tbody>
        <?php
        foreach($requests as $value){
            if (isset($value->accepted)) {
                $accepted = $value->accepted;
                $class = " class = \"table-success\" ";
            } else {
                $accepted = "Not accepted yet";
                $class = " class = \"table-secondary\" ";
            }
            echo "<tr>";
            echo "<td>$value->id</td>";
            echo "<td>$value->email</td>";
            echo "<td>$value->firstname $value->lastname</td>";
            echo "<td>$value->affiliation</td>";
            echo "<td>$value->title_research</td>";
            echo "<td>$value->timeline</td>";
            echo "<td$class>$accepted</td>";
            echo "<td><a class=\"btn-sm btn-primary\" href=\"".base_url()."admin/show_request/$value->id\">Show <i class=\"fas fa-eye\"></i></a> <a class=\"btn-sm btn-danger\" href=\"".base_url()."admin/delete_request/$value->id\" data-confirm=\"Are you sure you want to delete this request and his associed user ?\">Delete <i class=\"fas fa-trash\"></i></a>";
            echo "</tr>";
        }?>
    </tbody>
</table>
