<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3 bg-gradient-dark">
            <h2>Search Result against '<?= $_GET['search'] ?>'</h2>
        </div>
        <div class="row mt-lg-n4 mt-md-n3 mt-sm-n2 justify-content-center">
            <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow">
                    <div class="card-body">
                        <div class="container-fluid">
                            <table class="table table-hover table-striped table-bordered" id="list">
                                <colgroup>
                                    <col width="5%">
                                    <col width="15%">
                                    <col width="10%">
                                    <col width="15%">
                                    <col width="20%">
                                    <col width="20%">
                                    <col width="15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date Created</th>
                                        <th>Code</th>
                                        <th>Reported By</th>
                                        <th>Message</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if(isset($_GET['search'])):
                                    $i = 1;
                                    $qry = $conn->query("SELECT * from `request_list` where (fullname LIKE '%{$_GET['search']}%' or contact LIKE '%{$_GET['search']}%' or code LIKE '%{$_GET['search']}%') order by abs(unix_timestamp(date_created)) desc ");
                                        while($row = $qry->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++; ?></td>
                                            <td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                            <td><?php echo $row['code'] ?></td>
                                            <td><?php echo $row['fullname'] ?></td>
                                            <td><?php echo $row['message'] ?></td>
                                            <td><?php echo $row['location'] ?></td>
                                            <td align="center">
                                                <a href="./?p=report/view_report&id=<?= $row['id'] ?>" class="btn btn-flat btn-sm btn-light bg-gradient-light border">
                                                        <i class="fa fa-eye text-dark"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#list').find('th, td').addClass('py-1 px-2 align-middle')
    })
</script>