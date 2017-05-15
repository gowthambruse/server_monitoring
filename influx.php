
<?php


/*memory taking*/

function get_server_memory_usage(){
 
	$free = shell_exec('free');

	$free = (string)trim($free);
	$free_arr = explode("\n", $free);
	$mem = explode(" ", $free_arr[1]);
	$mem = array_filter($mem);
	$mem = array_merge($mem);
	$memory_usage = $mem[2]/$mem[1]*100; 
	return $memory_usage;
}


function get_server_cpu_usage(){
 
	$load = sys_getloadavg();
	return $load[0];
 
}
$ser_mem_usa=round( get_server_memory_usage(), 2 );
$ser_cpu_usa=get_server_cpu_usage()*10;



/****************Disk Space************************/



/* get disk space free (in bytes) */
$ser_dis_fre = disk_free_space("/");
/* and get disk space total (in bytes)  */
$ser_tot_spac = disk_total_space("/");
/* now we calculate the disk space used (in bytes) */
$ser_use_spac = $ser_tot_spac - $ser_dis_fre;
/* percentage of disk used - this will be used to also set the width % of the progress bar */
$ser_use_per = sprintf('%.2f',($ser_use_spac / $ser_tot_spac) * 100);

/* and we formate the size from bytes to MB, GB, etc. */
$ser_dis_fre = formatSize($ser_dis_fre);
$ser_use_spac = formatSize($ser_use_spac);
$ser_tot_spac = formatSize($ser_tot_spac);

function formatSize( $bytes )
{
      
        for( $i = 0; $bytes >= 1024 ; $bytes /= 1024, $i++ );
                return( round( $bytes, 2 ));
}

/**********curl request******/ 

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://45.33.43.250:8086/write?db=server");
curl_setopt($ch, CURLOPT_POST, 1);


echo $ser_dis_fre;



curl_setopt($ch, CURLOPT_POSTFIELDS,
            "cpu_load_short cpu_usage=$ser_cpu_usa,disk_free=$ser_dis_fre,disk_used=$ser_use_spac,disk_total=$ser_tot_spac,disk_used_percent=$ser_use_per,memory_usage=$ser_mem_usa");



curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

curl_close ($ch);



?>


<div class='progress'>
        <div class='prgtext'><?php echo $ser_use_per; ?>% Disk Used</div>
 <div class='prgtext'><?php echo $ser_mem_usa; ?>% Memory Used</div>
 <div class='prgtext'><?php echo $ser_cpu_usa; ?>% CPU Used</div>
        <div class='prgbar'></div>
        <div class='prginfo'>
                <span style='float: left;'><?php echo "$ser_use_spac of $ser_tot_spac used"; ?></span><br>
                <span style='float: left;'><?php echo "$ser_dis_fre of $ser_tot_spac free"; ?></span>
                <span style='clear: both;'></span>
        </div>
</div>


