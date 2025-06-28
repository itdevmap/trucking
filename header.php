<a href="main.php" class="logo">
    <span class="logo-mini" ><img class="stretch" src="img/logo_pendek.png" class="user-image" alt="User Image" ></span>
    <span class="logo-lg" style="background: url(img/logo_panjang.png)  no-repeat center;background-size: cover;">&nbsp;</span>
</a>

<nav class="navbar navbar-static-top" role="navigation">
	<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" >
		<span class="sr-only" style="font-size:35px !important">Toggle navigation</span>
	</a>
	
	<div class="sidebar-pt" style="font-size:15px !important" >
		<?php //echo $nilai_kurs;?>
	</div>
	
	
	<div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="img/user.png" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?php echo $nama_user; ?></span>
                </a>
				
                <ul class="dropdown-menu">
                                 
                  <li class="user-footer">
					<a href="gantipassword.php" class="btn btn-block btn-success" style="width:100%">Change Password</a>
					<a href="logout.php" class="btn btn-block btn-danger" style="width:100%">Sign out</a>                   
                  </li>
                </ul>
            </li>
        </ul>
    </div>	
</nav>

	