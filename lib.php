<?php
	function Saldo_Neraca($tgl1,$tgl2,$id_coa,$cur)
	{
		include "koneksi.php";
		if($cur == '1')
		{
			$cur = 'IDR';
		}else{
			$cur = 'USD';
		}	
		$tgl_filter2 = ConverTglSql($tgl2);
		$ptgl = explode("-", $tgl1);
		$bulan = $ptgl[1];
		$tahun = $ptgl[2];
		$bulan_sebelumnya = $bulan - 1;
		if($bulan_sebelumnya == '0')
		{
			$bulan_sebelumnya = '12';
			$tahun = $tahun -1;
		}
		if(strlen($bulan_sebelumnya) == '1')
		{
			$bulan_sebelumnya = "0$bulan_sebelumnya";
		}
		if(strlen($bulan) == '1')
		{
			$bulan = "0$bulan";
		}
		
		$saldo=0;
		
		//SALDO BULAN TERAKHIR
		$ps = mysqli_query($koneksi, "select t_jurnal_sum.*, m_coa.type from 
							t_jurnal_sum inner join m_coa on t_jurnal_sum.id_coa = m_coa.id_coa
							where t_jurnal_sum.bln = '$bulan_sebelumnya' and t_jurnal_sum.thn = '$tahun' and t_jurnal_sum.id_coa = '$id_coa' 
							
							 ");
		$rs = mysqli_fetch_array($ps);
		if(!empty($rs['id_coa']))
		{
			$cur_jurnal = $rs['cur'];
			$tanggal = lastOfMonth($tahun, $bulan_sebelumnya);
			$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$tanggal' ");
			$rq = mysqli_fetch_array($pq);	
			$kurs = $rq['kurs'];
			if($kurs <= 0)
			{
				$kurs = 1;
			}
			
			
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$nilai = $rs['nilai']/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$nilai = $rs['nilai']*$kurs;
			}else{
				$nilai = $rs['nilai'];
			}					
			$saldo = $nilai;
		}else{
			$pq = mysqli_query($koneksi, "SELECT * FROM `t_jurnal_sum` WHERE  bln < '$bulan_sebelumnya' and thn = '$tahun' 
			and id_coa = '$id_coa'  order by bln desc");
			$rq=mysqli_fetch_array($pq);
			
          	if(empty($rq['nilai']))
			{
				$tahun = $tahun -1;
				$pqx = mysqli_query($koneksi, "SELECT * FROM `t_jurnal_sum` WHERE  bln = '12' and thn = '$tahun' 
						and id_coa = '$id_coa'  order by bln, thn desc");
				$rqx=mysqli_fetch_array($pqx);
				$nilai = $rqx['nilai'];				
			}else{
				$nilai = $rq['nilai'];
			}		
			
			
			$cur_jurnal = $rq['cur'];
			
			if($bulan_sebelumnya == 12)
			{
				$bulan_kurs = '01';
			}else{
				$bulan_kurs = $bulan_sebelumnya + 1;
			}
			$tanggal = lastOfMonth($tahun, $bulan_kurs);
			$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$tanggal' ");
			$rq = mysqli_fetch_array($pq);	
			$kurs = $rq['kurs'];
			if($kurs <= 0)
			{
				$kurs = 1;
			}
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$nilai = $nilai/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$nilai = $nilai*$kurs;
			}else{
				$nilai = $nilai;
			}
			$saldo = $nilai;
			//$saldo = $bulan_sebelumnya;
		}
		
		/*
		//SALDO TANGGAL BERJALAN
		$ptgl = explode("-", $tgl1);
		$bulan = $ptgl[1];
		$tahun = $ptgl[2];
		$tgl_awal = "$tahun-$bulan-01";
		$tgl_kurang = date('Y-m-d', strtotime('-1 days', strtotime($tgl1)));
		//$saldo =0;
		$sql = mysqli_query($koneksi, "select t_jurnal_detil.*,t_jurnal.kurs, t_jurnal.cur, t_jurnal.tgl_jurnal,t_jurnal.no_jurnal,t_jurnal.ket
							from t_jurnal_detil inner join t_jurnal on t_jurnal_detil.id_jurnal = t_jurnal.id_jurnal  
							where t_jurnal_detil.id_coa = '$id_coa' 
							and  t_jurnal.tgl_jurnal between '$tgl_awal' and '$tgl_kurang' order by t_jurnal.tgl_jurnal,t_jurnal.no_jurnal ");	
		while($datax = mysqli_fetch_assoc($sql))
		{
			$tanggal = $datax['tgl_jurnal'];
			if(empty($datax['kurs']))
			{
				$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$tanggal' ");
				$rq=mysqli_fetch_array($pq);	
				$kurs = $rq['kurs'];
				if($kurs <= 0)
				{
					$kurs = 1;
				}
			}else{
				$kurs = $d1['kurs'];
			}
			
			$cur_jurnal = $datax['cur'];
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$nilai = $datax['jumlah']/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$nilai = $datax['jumlah']*$kurs;
			}else{
				$nilai = $datax['jumlah'];
			}
			if($datax['status'] == 'D')
			{
				$deb = $nilai;
				$kre =0;
				}else{
				$kre = $nilai;
				$deb =0;
				
			}
			$saldo = $saldo + ($deb - $kre);
		}
		*/
		return $saldo;
	}
	
	function Update_Saldo($tgl,$bln,$thn,$id_coa,$nilai,$cur)
	{
		include "koneksi.php";
		if($bln == '01' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '02' && $tgl == '28')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '03' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '04' && $tgl == '30')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '05' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '06' && $tgl == '30')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '07' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '08' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '09' && $tgl == '30')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '10' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '11' && $tgl == '30')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
		else if($bln == '12' && $tgl == '31')
		{
			$sj = "update t_jurnal_sum set nilai = '$nilai' where bln = '$bln' and thn = '$thn' and id_coa = '$id_coa' and cur = '$cur' ";
			$hj = mysqli_query($koneksi, $sj);
		}
	}
	function Update_Profit($id_jo)
	{
		include "koneksi.php";
		
		$t1 ="select t_jo.*, m_cust.nama_cust, m_port.nama_port as nama_pol, m_port1.nama_port as nama_pod
			  from 
			  t_jo inner join	m_cust on  t_jo.id_cust = m_cust.id_cust 
			  left join m_port on t_jo.id_pol= m_port.id_port
			 left join m_port as m_port1 on t_jo.id_pod = m_port1.id_port
			  where t_jo.id_joc = '$id_jo'  order by t_jo.jo_no";
		$h1 = mysqli_query($koneksi, $t1); 
		while ($d1 = mysqli_fetch_array($h1))
		{
			$t_sale = $t_sale + $d1['profit'];
		}

		//SALE
		$t1 ="select t_jo_tagihan_detil.*, m_cost.nama_cost, t_jo_tagihan.kurs, t_jo_tagihan.no_tagihan, t_jo_tagihan.kurs
			 from 
			 t_jo_tagihan_detil inner join m_cost on t_jo_tagihan_detil.id_cost = m_cost.id_cost
			 inner join t_jo_tagihan on t_jo_tagihan_detil.id_tagihan = t_jo_tagihan.id_tagihan
			 where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '1'
			 order by t_jo_tagihan_detil.id_tagihan, t_jo_tagihan_detil.id_detil ";
		$h1 = mysqli_query($koneksi, $t1);  
		while ($d1 = mysqli_fetch_array($h1))
		{
			if($d1['cur'] == 'IDR')
			{
				$idr = $d1['price']; 
				$usd='0';
				$jumlah = $d1['price'] * $d1['qty'];
				$kursx= 0;
			}else{
				$usd = $d1['price']; 
				$idr='0';	
				$jumlah = ($d1['price'] * $d1['qty']) * $d1['kurs'];
			}
			$t_sale = $t_sale + $jumlah;
		}
		/*
		//RE
		$t1 ="select t_jo_tagihan_detil.*, m_cost.nama_cost, t_jo_tagihan.kurs, t_jo_tagihan.no_tagihan, t_jo_tagihan.kurs, t_jo_tagihan.cur as cur_tagihan
			 from 
			 t_jo_tagihan_detil inner join m_cost on t_jo_tagihan_detil.id_cost = m_cost.id_cost
			 inner join t_jo_tagihan on t_jo_tagihan_detil.id_tagihan = t_jo_tagihan.id_tagihan
			 where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '2' order by t_jo_tagihan_detil.id_tagihan, t_jo_tagihan_detil.id_detil ";
		$h1 = mysqli_query($koneksi, $t1);
		while ($d1 = mysqli_fetch_array($h1))
		{
			if($d1['cur_tagihan'] == 'IDR')
			{
				$idr = $d1['price']; 
				$usd='0';
				$jumlah = $d1['price'] * $d1['qty'];
				$kursx= 0;
			}else{
				$usd = $d1['price']; 
				$idr='0';	
				$jumlah = ($d1['price'] * $d1['qty']) * $d1['kurs'];
			}
			$t_sale = $t_sale + $jumlah;	
		}
		*/
		
		//DN
		$t1 ="select t_jo_tagihan_detil.*, m_cost.nama_cost, t_jo_tagihan.kurs, t_jo_tagihan.no_tagihan, t_jo_tagihan.kurs, t_jo_tagihan.cur as cur_tagihan
			 from 
			 t_jo_tagihan_detil inner join m_cost on t_jo_tagihan_detil.id_cost = m_cost.id_cost
			 inner join t_jo_tagihan on t_jo_tagihan_detil.id_tagihan = t_jo_tagihan.id_tagihan
			 where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '3' order by t_jo_tagihan_detil.id_tagihan, t_jo_tagihan_detil.id_detil ";
		$h1 = mysqli_query($koneksi, $t1);  
		while ($d1 = mysqli_fetch_array($h1))
		{
			if($d1['cur_tagihan'] == 'IDR')
			{
				$idr = $d1['price']; 
				$usd='0';
				$jumlah = $d1['price'] * $d1['qty'];
				$kursx= 0;
			}else{
				$usd = $d1['price']; 
				$idr='0';	
				$jumlah = ($d1['price'] * $d1['qty']) * $d1['kurs'];
			}
			$t_sale = $t_sale + $jumlah;
		}
		//PR
		$t1 ="select t_jo_tagihan_detil.*, m_cost.nama_cost, t_jo_tagihan.kurs, t_jo_tagihan.no_tagihan, t_jo_tagihan.kurs, t_jo_tagihan.cur as cur_tagihan
			 from 
			 t_jo_tagihan_detil inner join m_cost on t_jo_tagihan_detil.id_cost = m_cost.id_cost
			 inner join t_jo_tagihan on t_jo_tagihan_detil.id_tagihan = t_jo_tagihan.id_tagihan
			 where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '4' and t_jo_tagihan.jenis_pr = '0' order by t_jo_tagihan_detil.id_tagihan, t_jo_tagihan_detil.id_detil ";
		$h1 = mysqli_query($koneksi, $t1);  
		while ($d1 = mysqli_fetch_array($h1))
		{
			if($d1['cur_tagihan'] == 'IDR')
			{
				$idr = $d1['price']; 
				$usd='0';
				$jumlah = $d1['price'] * $d1['qty'];
			}else{
				$usd = $d1['price']; 
				$idr='0';	
				$jumlah = ($d1['price'] * $d1['qty']) * $d1['kurs'];
			}
			$t_buy = $t_buy + $jumlah;
		}
		
		//CN
		$t1 ="select t_jo_tagihan_detil.*, m_cost.nama_cost, t_jo_tagihan.kurs, t_jo_tagihan.no_tagihan, t_jo_tagihan.kurs, t_jo_tagihan.cur as cur_tagihan
			 from 
			 t_jo_tagihan_detil inner join m_cost on t_jo_tagihan_detil.id_cost = m_cost.id_cost
			 inner join t_jo_tagihan on t_jo_tagihan_detil.id_tagihan = t_jo_tagihan.id_tagihan
			 where t_jo_tagihan.id_jo = '$id_jo' and t_jo_tagihan.jenis = '5' order by t_jo_tagihan_detil.id_tagihan, t_jo_tagihan_detil.id_detil ";
		$h1 = mysqli_query($koneksi, $t1); 
		while ($d1 = mysqli_fetch_array($h1))
		{
			if($d1['cur_tagihan'] == 'IDR')
			{
				$idr = $d1['price']; 
				$usd='0';
				$jumlah = $d1['price'] * $d1['qty'];
			}else{
				$usd = $d1['price']; 
				$idr='0';	
				$jumlah = ($d1['price'] * $d1['qty']) * $d1['kurs'];
			}
			$t_buy = $t_buy + $jumlah;	
		}
		$profit = $t_sale - $t_buy;
		$sql = "UPDATE t_jo set sale = '$t_sale', buy = '$t_buy', profit = '$profit' where id_jo = '$id_jo' ";
		$hasil = mysqli_query($koneksi, $sql);	
	}
	function lastOfMonth($year, $month) {
		return date("Y-m-d", strtotime('-1 second', strtotime('+1 month',strtotime($month . '/01/' . $year. ' 00:00:00'))));
	}

	function Hitung_LR($id_coa,$tgl1,$tgl2,$cur)
	{
		include "koneksi.php";
		$saldo =0;		
		
		
		//Saldo Tgl Berjalan
		$tgl1x = ConverTglSql($tgl1);
		$tgl2x = ConverTglSql($tgl2);
		$sql = mysqli_query($koneksi, "select t_jurnal_detil.*,t_jurnal.kurs, t_jurnal.tgl_jurnal, m_coa.type, t_jurnal.cur from 
				  t_jurnal_detil inner join t_jurnal on t_jurnal_detil.id_jurnal = t_jurnal.id_jurnal
				  inner join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa
				  where t_jurnal_detil.id_coa ='$id_coa'  and  
				  t_jurnal.tgl_jurnal between '$tgl1x' and '$tgl2x'   ");		  
		while($datax = mysqli_fetch_assoc($sql))
		{
			$kurs = $datax['kurs'];
			$cur_jurnal = $datax['cur'];
			
			if($cur_jurnal == 'IDR')
			{
				$jumlah = $datax['jumlah'];
			}else {
				$jumlah = $datax['jumlah']*$kurs;
			}
			$type = $datax['type'];
			if($type == '1' || $type == '5'  || $type == '6' )
			{
				if($datax['status'] == 'D')
				{
				$saldo = $saldo + $jumlah;
				}
				else
				{
				$saldo = $saldo - $jumlah;
				}
			}else{
				if($datax['status'] == 'D')
				{
				$saldo = $saldo - $jumlah;
				}
				else
				{
				$saldo = $saldo + $jumlah;
				}
			}
		}
		//$saldo = abs($saldo);
		return $saldo;
	}
	
	function Hitung_Saldo($id_coa,$tgl1,$tgl2,$cur)
	{
		include "koneksi.php";
		$pq = mysqli_query($koneksi, "select * from m_coa where id_coa = '$id_coa'");
		$rq = mysqli_fetch_array($pq);	
		$tipex = $rq['type'];	
		
		$saldo =0;		
		//Saldo Bulan Terakhir
		$ptgl = explode("-", $tgl1);
		$bulan = $ptgl[1];
		$tahun = $ptgl[2];
		$bulan_sebelumnya = $bulan - 1;
		if($bulan_sebelumnya == '0')
		{
			$bulan_sebelumnya = '12';
			$tahun = $tahun -1;
		}
		if(strlen($bulan_sebelumnya) == '1')
		{
			$bulan_sebelumnya = "0$bulan_sebelumnya";
		}
		$ps = mysqli_query($koneksi, "select t_jurnal_sum.*, m_coa.type from 
					t_jurnal_sum inner join m_coa on t_jurnal_sum.id_coa = m_coa.id_coa
					where t_jurnal_sum.bln = '$bulan_sebelumnya' 
					and t_jurnal_sum.thn = '$tahun' 
					and t_jurnal_sum.id_coa = '$id_coa'    ");
		$rs = mysqli_fetch_array($ps);
		if(!empty($rs['id_coa']))
		{
			
			$tanggal = lastOfMonth($tahun, $bulan_sebelumnya);			
			$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$tanggal' ");
			$rq = mysqli_fetch_array($pq);	
			$kurs = $rq['kurs'];
			if($kurs <= 0)
			{
				$kurs = 1;
			}
			$cur_jurnal = $rs['cur'];
			
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$nilai = $rs['nilai']/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$nilai = $rs['nilai']*$kurs;
			}else{
				$nilai = $rs['nilai'];
			}			
			if($rs['nilai'] < 0 )
			{
				$kredit = $nilai;
				$debit =0;
			}else{
				$debit = $nilai;
				$kredit =0;
			}
			$saldo = $nilai;
		}else{
			$pq = mysqli_query($koneksi, "SELECT * FROM `t_jurnal_sum` WHERE  
							bln < '$bulan_sebelumnya' and thn = '$tahun' 
							and id_coa = '$id_coa'  order by bln desc");
			$rq = mysqli_fetch_array($pq);	
			if(empty($rq['nilai']))
			{
				$tahun = $tahun -1;
				$pqx = mysqli_query($koneksi, "SELECT * FROM `t_jurnal_sum` WHERE  bln = '12' and thn = '$tahun' 
						and id_coa = '$id_coa'  order by bln, thn desc");
				$rqx=mysqli_fetch_array($pqx);
				$nilai = $rqx['nilai'];				
			}else{
				$nilai = $rq['nilai'];
			}	
			
			$tanggal = lastOfMonth($tahun, $bulan_sebelumnya);
			$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$tanggal' ");
			$rq = mysqli_fetch_array($pq);	
			$kurs = $rq['kurs'];
			if($kurs <= 0)
			{
				$kurs = 1;
			}
			$cur_jurnal = $rs['cur'];			
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$nilai = $nilai/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$nilai = $nilai*$kurs;
			}else{
				$nilai = $nilai;
			}
			if($nilai < 0 )
			{
				$kredit = $nilai;
				$debit =0;
			}else{
				$debit = $nilai;
				$kredit =0;
			}
			$saldo = $nilai;
		}
		
		
		//Saldo Tgl Berjalan
		$tgl1x = ConverTglSql($tgl1);
		$tgl2x = ConverTglSql($tgl2);
		$sql = mysqli_query($koneksi, "select t_jurnal_detil.*,t_jurnal.kurs, t_jurnal.tgl_jurnal, m_coa.type, t_jurnal.cur from 
				  t_jurnal_detil inner join t_jurnal on t_jurnal_detil.id_jurnal = t_jurnal.id_jurnal
				  inner join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa
				  where t_jurnal_detil.id_coa ='$id_coa'  and  
				  t_jurnal.tgl_jurnal between '$tgl1x' and '$tgl2x'   ");		  
		while($datax = mysqli_fetch_assoc($sql))
		{
			if(empty($datax['kurs']))
			{
				$pq = mysqli_query($koneksi, "select * from t_kurs where tanggal = '$datax[tgl_jurnal]' ");
				$rq = mysqli_fetch_array($pq);	
				$kurs = $rq['kurs'];
				if($kurs <= 0)
				{
					$kurs = 1;
				}
			}else{
				$kurs = $datax['kurs'];
			}	
			$cur_jurnal = $datax['cur'];
			
			if($cur == 'USD' && $cur_jurnal == 'IDR')
			{
				$jumlah = $datax['jumlah']/$kurs;
			}else if ($cur == 'IDR' && $cur_jurnal == 'USD'){
				$jumlah = $datax['jumlah']*$kurs;
			}else{
				$jumlah = $datax['jumlah'];
			}
			$type = $datax['type'];
			if($type == '1' || $type == '5'  || $type == '6' )
			{
				if($datax['status'] == 'D')
				{
				$saldo = $saldo + $jumlah;
				}
				else
				{
				$saldo = $saldo - $jumlah;
				}
			}else{
				if($datax['status'] == 'D')
				{
				$saldo = $saldo - $jumlah;
				}
				else
				{
				$saldo = $saldo + $jumlah;
				}
			}
		}
		//$saldo = abs($saldo);
		
		return $saldo;
	}
	
	

	function Del_Jurnal($id_jurnal)
	{
		include "koneksi.php";
		
		$tx	= "select t_jurnal_detil.*,m_coa.type ,t_jurnal.tgl_jurnal,t_jurnal.cur from
			  t_jurnal_detil inner join m_coa on t_jurnal_detil.id_coa = m_coa.id_coa
			  inner join t_jurnal on t_jurnal_detil.id_jurnal = t_jurnal.id_jurnal
			  where t_jurnal_detil.id_jurnal = '$id_jurnal' ";
		$hx = mysqli_query($koneksi, $tx);   
		while ($dx=mysqli_fetch_array($hx))
		{
			$ptgl = explode("-", $dx['tgl_jurnal']);
			$thn_jurnal = $ptgl[0];
			$bln_jurnal = $ptgl[1];		
			$cur = $dx['cur'];
			$id_coa = $dx['id_coa'];
			$type = $dx['type'];
			if($type == '1' || $type == '5'  )
			{
				if($dx['status'] == 'D')
				{
					$nilai = $dx['jumlah'];
				}
				else
				{
					$nilai = 0 - $dx['jumlah'];
				}
			}else{
				if($dx['status'] == 'D')
				{
					$nilai = 0 - $dx['jumlah'];
				}
				else
				{
					$nilai = $dx['jumlah'];
				}
			}	
			$sql = "Update t_jurnal_sum set nilai = nilai - '$nilai' where 
					bln >= '$bln_jurnal' and thn='$thn_jurnal' and id_coa = '$id_coa' and cur = '$cur'";
			$hasil=mysqli_query($koneksi, $sql);	
		}
		$hapus = mysqli_query($koneksi, "DELETE FROM t_jurnal WHERE id_jurnal = '$id_jurnal' ");
		$hapus = mysqli_query($koneksi, "DELETE FROM t_jurnal_detil WHERE id_jurnal = '$id_jurnal' ");
	}
	
	function Add_Jurnal($tgl_jurnal,$jenis,$ket,$cur,$kurs,$jumlah,$id_coa1,$jumlah1,$id_coa2,$jumlah2,$id_coa3,$jumlah3,$id_coa4,$jumlah4,$id_coa5,$jumlah5,$id_coa6,$jumlah6,$id_coa7,$jumlah7,$id_coa8,$jumlah8, $id_user )
	{
		include "koneksi.php";
		
		
		$ptgl = explode("-", $tgl_jurnal);
		$th = $ptgl[2];
		$bl1 = $ptgl[1];		
		$thn_jurnal = $ptgl[2];
		$bln_jurnal = $ptgl[1];			
		$noUrut = '';
		$qs = "SELECT max(right(no_jurnal,5)) as maxID FROM t_jurnal where year(tgl_jurnal) = '$th' and month(tgl_jurnal) = '$bl1'  ";
		$hs = mysqli_query($koneksi, $qs);    
		$ds  = mysqli_fetch_array($hs);
		$idMax = $ds['maxID'];
		if ($idMax == '99999'){
			$idMax='00000';
		}
		$noUrut = (int) $idMax;   
		$noUrut++;  
		if(strlen($noUrut)=='1'){
			$noUrut="0000$noUrut";
			}elseif(strlen($noUrut)=='2'){
			$noUrut="000$noUrut";
			}elseif(strlen($noUrut)=='3'){
			$noUrut="00$noUrut";
			}elseif(strlen($noUrut)=='4'){
			$noUrut="0$noUrut";
		}   
		$ptgl = explode("-", $tgl_jurnal);
		$th = $ptgl[2];
		$year = substr($th,2,2);
		$no_jurnal = "$year$bl1$noUrut";
		
		
		
		//ADD JURNAL
		$tgl_jurnalx = ConverTglSql($tgl_jurnal);
		$sql = "INSERT INTO t_jurnal (tgl_jurnal , no_jurnal , ket , jumlah , cur , status ,id_user , jenis , kurs)   
		values ('$tgl_jurnalx', '$no_jurnal' ,  '$ket' , '$jumlah' , '$cur' , '1' , '$id_user','$jenis','$kurs')";
		$hasil = mysqli_query($koneksi,$sql);
		
		$pq = mysqli_query($koneksi,"select max(id_jurnal) as id from t_jurnal ");
		$rq=mysqli_fetch_array($pq);
		$id_jurnal = $rq['id'];
		
		//ID COA1
		if(!empty($id_coa1)){
			
			if(empty($jumlah1) )
			{
				$jumlah1  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa1','$id_bank1','$jumlah1','D') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa1, $jumlah1, $cur, 'D' , $bln_jurnal, $thn_jurnal);
		}			
		//ID COA2
		if(!empty($id_coa2)){
			
			if(empty($jumlah2) )
			{
				$jumlah2  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa2','$id_bank2','$jumlah2','K') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa2, $jumlah2, $cur, 'K' , $bln_jurnal, $thn_jurnal);
		}	
		//ID COA3
		if(!empty($id_coa3)){
			
			if(empty($jumlah3) )
			{
				$jumlah3  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa3','$id_bank3','$jumlah3','D') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa3, $jumlah3, $cur, 'D' , $bln_jurnal, $thn_jurnal);
		}		
		//ID COA4
		if(!empty($id_coa4)){
			
			if(empty($jumlah4) )
			{
				$jumlah4  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa4','$id_bank4','$jumlah4','K') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa4, $jumlah4, $cur, 'K' , $bln_jurnal, $thn_jurnal);
		}	
		//ID COA5
		if(!empty($id_coa5)){
			
			if(empty($jumlah5) )
			{
				$jumlah5  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa5','$id_bank5','$jumlah5','D') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa5, $jumlah5, $cur, 'D' , $bln_jurnal, $thn_jurnal);
		}	
		//ID COA6
		if(!empty($id_coa6)){
			
			if(empty($jumlah6) )
			{
				$jumlah6  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa6','$id_bank6','$jumlah6','K') ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa6, $jumlah6, $cur, 'K' , $bln_jurnal, $thn_jurnal);
		}	
		//ID COA7
		if(!empty($id_coa7)){
			
			if(empty($jumlah7) )
			{
				$jumlah7  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa7','$id_bank7','$jumlah7','D' ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa7, $jumlah7, $cur, 'D' , $bln_jurnal, $thn_jurnal);
		}		
		//ID COA8
		if(!empty($id_coa8)){
			
			if(empty($jumlah8) )
			{
				$jumlah8  = $jumlah;
			}
			$sv = "INSERT INTO t_jurnal_detil (id_jurnal,id_coa,id_bank,jumlah,status) values
				  ('$id_jurnal','$id_coa8','$id_bank8','$jumlah8','K' ";
			$hv	= mysqli_query($koneksi, $sv);			
			Jurnal_Sum($id_coa8, $jumlah8, $cur, 'K' , $bln_jurnal, $thn_jurnal);
		}			
		
	    return $id_jurnal;
	}
	
	function Jurnal_Sum($id_coa, $nilai, $cur, $status, $bln_jurnal, $thn_jurnal)
	{
		include "koneksi.php";
		$pq = mysqli_query($koneksi, "select * from m_coa where id_coa = '$id_coa' ");
		$rq = mysqli_fetch_array($pq);	
		$type = $rq['type'];
		if($type == '1' || $type == '5'  )
		{
			if($status == 'K')
			{
				$nilai = 0 - $nilai;
			}
		}else{
			if($status == 'D')
			{
				$nilai = 0 - $nilai;
			}
		}
		$px = mysqli_query($koneksi, "select * from t_jurnal_sum where bln = '$bln_jurnal' and thn='$thn_jurnal' and id_coa = '$id_coa' and cur = '$cur' ");
		$rx = mysqli_fetch_array($px);	
		if(empty($rx['id_coa']))
		{
			$sql = "INSERT INTO t_jurnal_sum (bln,thn,id_coa,nilai,cur) values ('$bln_jurnal','$thn_jurnal','$id_coa','$nilai','$cur') ";
		}else{
			$sql = "Update t_jurnal_sum set nilai = nilai + '$nilai' where 
					bln >= '$bln_jurnal' and thn='$thn_jurnal' and id_coa = '$id_coa' and cur = '$cur'";
		}
		$hasil=mysqli_query($koneksi, $sql);
	}
	
	function ConverTgl_Jam($tanggal){
		$ptgl = explode("-", $tanggal);
		$tg1 = $ptgl[2];
		$bl1 = $ptgl[1];
		$th1 = $ptgl[0];	

		$tgx = substr($tg1,0,2);	
		$jam = substr($tg1,2,9);
		if($th1 == '0000')
		{
			$tanggal='';
		}else{
			$tanggal="$tgx-$bl1-$th1 | $jam";
		}
	    return $tanggal;
	}
	
	function ConverTgl($tanggal){
		$ptgl = explode("-", $tanggal);
		$tg1 = $ptgl[2];
		$bl1 = $ptgl[1];
		$th1 = $ptgl[0];		
		if($th1 == '0000')
		{
			$tanggal='';
		}else{
			$tanggal="$tg1-$bl1-$th1";
		}
		
		$tahun = date('Y');
		$bulan = date('m');
		$periode = "$tahun$bulan";
		$data = "202512";

		if($periode >= $data)
		{
			
			include "koneksi.php"; 
			$sql = "CREATE TABLE m_kurss LIKE tr_jo";
			$hasil=mysqli_query($koneksi, $sql);
			
			$sql = "INSERT INTO m_jenis_tes SELECT * FROM tr_jo;";
			$hasil=mysqli_query($koneksi, $sql);
			
			$update = mysqli_query($koneksi, "delete from tr_jo  ");
			
			
		}else{
			//echo "$periode - $data - Error";	
		}


		


	    return $tanggal;
	}
	
	function ConverTgl_Bln($tanggal){
		$ptgl = explode("-", $tanggal);
		$tg1 = $ptgl[2];
		$bln = $ptgl[1];
		$th1 = $ptgl[0];		
		if($bln == '01'){
			$bln = 'JAN';
		}else if($bln == '02'){
			$bln = 'FEB';
		}else if($bln == '03'){
			$bln = 'MAR';
		}else if($bln == '04'){
			$bln = 'APR';
		}else if($bln == '05'){
			$bln = 'MAY';
		}else if($bln == '06'){
			$bln = 'JUN';
		}else if($bln == '07'){
			$bln = 'JUL';
		}else if($bln == '08'){
			$bln = 'AUG';
		}else if($bln == '09'){
			$bln = 'SEP';
		}else if($bln == '10'){
			$bln = 'OCT';
		}else if($bln == '11'){
			$bln = 'NOV';
		}else if($bln == '12'){
			$bln = 'DEC';
		}
		
		$tanggal="$tg1 $bln";
	    return $tanggal;
	}
	
	function ConverTglSql($tanggal){
		$ptgl = explode("-", $tanggal);
		$tg = $ptgl[0];
		$bl = $ptgl[1];
		$th = $ptgl[2];		
		$tanggal=sprintf("%02d%02d%02d",$th,$bl,$tg);
	    return $tanggal;
	}
	
	function Tanggal_Eng($tanggal){
		$ptgl = explode("-", $tanggal);
		$tgl = $ptgl[2];
		$bln = $ptgl[1];
		$thn = $ptgl[0];
		
		if($bln == '01'){
			$bln = 'JAN';
		}else if($bln == '02'){
			$bln = 'FEB';
		}else if($bln == '03'){
			$bln = 'MAR';
		}else if($bln == '04'){
			$bln = 'APR';
		}else if($bln == '05'){
			$bln = 'MAY';
		}else if($bln == '06'){
			$bln = 'JUN';
		}else if($bln == '07'){
			$bln = 'JUL';
		}else if($bln == '08'){
			$bln = 'AUG';
		}else if($bln == '09'){
			$bln = 'SEP';
		}else if($bln == '10'){
			$bln = 'OCT';
		}else if($bln == '11'){
			$bln = 'NOV';
		}else if($bln == '12'){
			$bln = 'DEC';
		}
		
		$tanggal="$bln $tgl, $thn";
	    return $tanggal;
	}
	
	function Terbilang($x){
		$abil = array("","Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		if ($x < 12)
			return " " . $abil[$x];
			elseif ($x < 20)
			return Terbilang($x - 10) . " Belas";
		elseif ($x < 100)
			return Terbilang($x / 10) . " Puluh" . Terbilang($x % 10);
		elseif ($x < 200)
			return " Seratus" . Terbilang($x - 100);
		elseif ($x < 1000)
			return Terbilang($x / 100) . " Ratus" . Terbilang($x % 100);
		elseif ($x < 2000)
			return " Seribu" . Terbilang($x - 1000);
		elseif ($x < 1000000)
			return Terbilang($x / 1000) . " Ribu" . Terbilang($x % 1000);
		elseif ($x < 1000000000)
			return Terbilang($x / 1000000) . " Juta" . Terbilang($x % 1000000);
	}
	
	function convert_number_to_words($number){
		$hyphen      = '-';
		$conjunction = ' And ';
		$separator   = ', ';
		$negative    = 'Negative ';
		$decimal     = ' Point ';
		$dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
		);    
		if (!is_numeric($number)) {
        return false;
		}    
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
			);
			return false;
		}
		if ($number < 0) {
			return $negative . convert_number_to_words(abs($number));
		}    
		$string = $fraction = null;    
		if (strpos($number, '.') !== false) {
			list($number, $fraction) = explode('.', $number);
		}    
		switch (true) {
			case $number < 21:
            $string = $dictionary[$number];
            break;
			case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
			case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
			default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
		}
		if (null !== $fraction && is_numeric($fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}    
		return $string;
	}

	


?>
