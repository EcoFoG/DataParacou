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
        <a class="btn" href="<?php echo base_url().'main' ?>">Retour au site</a>
    </form>
  </div>
</nav>
<script type="text/javascript">
$(document).ready(function() {
    $('#request-table').DataTable();
} );
</script>
<?php echo $flash_message; ?>
<form method="get">
    <input type="submit" value="Export to CSV" name="csv" />
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
            $accepted = isset($value->accepted) ? $value->accepted : "Not accepted yet";
            echo "<tr>";
            echo "<td>$value->id</td>";
            echo "<td>$value->email</td>";
            echo "<td>$value->firstname $value->lastname</td>";
            echo "<td>$value->affiliation</td>";
            echo "<td>$value->title_research</td>";
            echo "<td>$value->timeline</td>";
            echo "<td>$accepted</td>";
            echo "<td><a href=\"".base_url()."admin/show_request/$value->id\">Show request</a> <a href=\"".base_url()."admin/delete_request/$value->id\" data-confirm=\"Are you sure you want to delete this request ?\">Delete</a>";
            echo "</tr>";
        }?>
    </tbody>
</table>
