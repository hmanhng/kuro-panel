<?php

function color($value) {
if($value == 1) {
return "#0000FF";
} else {
return "#FF0000";
}
}
?>


<?= $this->extend('Layout/Starter') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-dark" role="alert">
            <strong>INFO :</strong> Search specify user by their (username, fullname, saldo or uplink).
        </div>
        <div class="card shadow-lg p-0 mb-5 ">
            <div class="card-body text-dark text-center h6 p-3">
                <h2>𝐌𝐚𝐧𝐚𝐠𝐞 𝐔𝐬𝐞𝐫𝐬</h2>
            </div>
            <div class="card-body">
                <?php if ($user_list) : ?>

                <div class="table-responsive">
                    <table class="table table-borderless table-hover text-center" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="row">ID</th>
                                <th>Username</th>
                                <th>Fullname</th>
                                <th>Level</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th>Uplink</th>
                                <th>Expiration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user_list as $u) : ?>
                            <tr>
                                <td><?= $u->id_users ?></td>
                                <td><?= $u->username ?></td>
                                <td><?= $u->fullname ?></td>
                                <td style="color: <?= color($u->level) ?>;">
                                <?php if($u->level == 1) : ?>
                                     Owner
                                <?php elseif($u->level == 2) : ?>
                                     Admin
                                <?php else : ?>
                                     Reseller
                                <?php endif; ?>
                                </td>
                                <td style="color: <?= color($u->level) ?>;">
                                <?php if($u->level == 1) : ?>
                                     &mstpos;
                                <?php else : ?>
                                      <?= $u->saldo ?>
                                <?php endif; ?>
                                </td>
                                <td style="color: <?= color($u->status) ?>;">
                                <?php if($u->status == 1) : ?>
                                      Active
                                <?php elseif($u->status == 2) : ?>
                                      Banned/Blocked
                                <?php else : ?>
                                      Expired
                                <?php endif; ?>
                                </td>
                                <td><?= $u->uplink ?></td>
                                <td><?= $u->expiration_date ?></td>
                                <td>
                                <a href="user/<?php echo $u->id_users ?>" class="btn btn-dark btn-sm"><i class="bi bi-pencil-square"></i></a><br></br>
                                <a href="user/delete/<?php echo $u->id_users ?>" input type="submit" value="deleteUser"class="btn btn-dark btn-sm" onclick='return deleteUser()'>
                                    <i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                       <tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
</br></br></br>
<?= $this->endSection() ?>

<?= $this->section('css') ?>
<?= link_tag("https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css") ?>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= script_tag("https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js") ?>

<?= script_tag("https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js") ?>

<script>
 function deleteUser() 
 {
     return confirm('Are you sure you want to delete this user?');
 }   
    
    
    
    
   // function deleteUser(user) {
    //    Swal.fire({
        //    title: 'Are you sure?',
       //     text: "You won't be able to revert this!",
     //       icon: 'warning',
     //       showCancelButton: true,
     //       confirmButtonColor: '#3085d6',
     //       cancelButtonColor: '#d33',
     //       confirmButtonText: 'Yes, Delete'
     //   }).then((result) => {
      //      if (result.isConfirmed) {
      //          Toast.fire({
       //             icon: 'info',
       //             title: 'Please wait...'
      //          })
//
       //         var base_url = window.location.origin;
       //         var api_url = `${base_url}user/delete`;
        //        $.getJSON(api_url, {
        //                user: user,
        //            },
        //            function(data, textStatus, jqXHR) {
         //               if (textStatus == 'success') {
         //                   if (data.registered) {
          //                      if (data.delete) {
            //                        $(`#user-${user}`).html(`0/${data.user}`);
            //                        Swal.fire(
           //                             'Delete!',
           //                             'Your user has been deleted.',
           //                             'success'
            //                        )
            //                    } else {
        //                        Swal.fire(
            //                            'Failed!',
      //                                  data.devices_total ? "You don't have any access to this user." : "User already deleted.",
       //                                 data.devices_total ? 'error' : 'warning'
        //                            )
         //                       }
         //                   } else {
          //                      Swal.fire(
          //                          'Failed!',
          //                          "User no longer exists.",
          //                          'error'
           //                     )
           //                 }
           //             }
           //         }
           //     );
           // }
      //  });
    //}</script>
<?= $this->endSection() ?>