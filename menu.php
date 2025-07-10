
<section class="sidebar">
	<ul class="sidebar-menu">
		
		<!--
		<div class="user-panel" style="background:#ddd;color:#000">
			<div class="pull-left image">
				<img src="img/userx.png" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info" >
				<p style="margin-top:5px;margin-bottom:5px !important;color:#195e99"><u><?php echo $nama_user;?></u></p>
				<a href="#" style="color:#000"><?php echo $nama_role;?></a>
			</div>
		</div>
		-->
		
		<li class="treeview" style="padding:1px"></li>
	
		<?php 
		$tampil1="SELECT m_menu_tr.*, m_role_akses_tr.id_role from m_menu_tr inner join m_role_akses_tr on m_menu_tr.id_menu = m_role_akses_tr.id_menu 
						where m_role_akses_tr.id_role = '$id_role' and m_menu_tr.id_parent ='0' and m_role_akses_tr.m_view='1' order by m_menu_tr.urut  ";
		$hasil1=mysqli_query($koneksi, $tampil1);       
		while ($data1=mysqli_fetch_array($hasil1)){ 
			$sql = mysqli_query($koneksi, "SELECT count(*) as jml from m_menu_tr where id_parent = '$data1[id_menu]' ");
			$row=mysqli_fetch_array($sql);
		?>
			<li class="treeview">
				<a href="<?php echo $data1['link'];?>" style="background-color: <?php echo $data1['warna'];?>;">
					<i class="<?php echo $data1['img'];?>" style="font-size:15px;padding-right:1px;"></i>
					<span><?php echo $data1['nama_menu']; ?></span>
					<?php if($row['jml'] >0){?>						
					<i class="fa fa-angle-left pull-right"></i>
					<?php }?>
				</a>
				
				<?php if($row['jml'] > 0){?>
				<ul class="treeview-menu">						
					<?php 
					$tampil2="SELECT m_menu_tr.*, m_role_akses_tr.id_role from m_menu_tr inner join m_role_akses_tr on m_menu_tr.id_menu = m_role_akses_tr.id_menu 
						where m_role_akses_tr.id_role = '$id_role' and  m_menu_tr.id_parent = '$data1[id_menu]' 
						and m_role_akses_tr.m_view='1'  order by m_menu_tr.urut asc";
					$hasil2=mysqli_query($koneksi, $tampil2);       
					while ($data2=mysqli_fetch_array($hasil2)){ 
					?>
					<li><a href="<?php echo $data2['link'];?>"><i class="fa fa-th"></i> <?php echo $data2['nama_menu'];?></a></li>
					<?php }?>
				</ul>
				<?php }?>
				
			</li>
		<?php }?>
			
	</ul>
</section>
