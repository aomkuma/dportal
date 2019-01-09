<?php 
include('header.php');

?>
<header id="myCarousel" class="carousel slide">
		<!-- Indicators -->
<meta charset="UTF-8">		
		<?php 
			$sql = " select max(banner_id) as mbn from t_banner where active = 1";
	
			
			$result = mysql_query($sql, $conn);
			$row = mysql_fetch_array($result);
			$MaxB = $row['mbn'];
			//echo $MaxB;

			
		
			$sql = "select * from t_banner where active = 1 order by sort_order ASC ";
			$objQuery = mysql_query($sql);
				echo "<div class='carousel-inner'>";
					while ($row = mysql_fetch_array($objQuery)) {
						if($row['banner_id'] == $MaxB){
							echo "<div class='item active'>";
						}else{
							echo "<div class='item'>";
						}
						echo "  <img src='".$row['images']."' class='fill' alt='".$row['title']."'>";
						if($row['title'] == ""){
							
						}else{
							echo " <div class='carousel-caption text-left'>
									
									<h4><span class='th'>".$row['title']."</span>
										<span class='en'>".$row['title_en']."</span></h4>
									<div class='en'>
										".$row['content_en']."
										<a class='btnreadmore' href='banner_detail.php?id=".encode($row['banner_id'])."' >อ่านต่อ</a>
									</div>
									<div class='th'>
										".$row['content']."
										<a class='btnreadmore' href='banner_detail.php?id=".encode($row['banner_id'])."' >อ่านต่อ</a>
									</div>
									</div>
									";
							
						}
						echo"
					</div>";
					
					}
					
			echo "</div>";
		
		?>
        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"></span>
        </a>
    </header>
	
	

<!-- Page Content -->
    <div class="container">
		<!-- Gallery -->
      
				<h2 class="page-header"><span class="th">ภาพกิจกรรม</span><span class="en">ภาพกิจกรรม</span></h2>
				<hr class='border' />
				<p class="text-right">
					<a href="gallery_list.php" style="float: right; margin-top: -52px;">
					<span class="en">ดูภาพกิจกรรมทั้งหมด</span>
					<span class="th">ดูภาพกิจกรรมทั้งหมด</span>
					</a>
				</p>
           
					<div id="myCarousel1" class="carousel">
			<?php 
			$sql = "
					select * from t_gallery where active = 1 and flag_index = 1
							order by sort_order DESC limit 6";
							
							//echo $sql;
			$objQuery = mysql_query($sql);
				echo "<div class='carousel-inner'>";
				echo "<div class='item active' style='padding: 0px 35px;'>";
					while ($row = mysql_fetch_array($objQuery)) {
						
						
							echo "<div class='col-md-2 col-sm-4 col-xs-6' style='height: 225px;'>
										
										<a href='gallery_detail.php?Id=".encode($row['gallery_id'])."'>
											<img class='img-responsive img-hover' src='".$row['images']."' alt='".$row['title']."'>
										</a>
										<h5><span class='th'>".$row['title']."</span>
										   <span class='en'>".$row['title_en']."</span></h5>
									  </div>";
									  
						
					}

				echo"</div>";
			
				
				$sql = "
						select * from t_gallery 
						where gallery_id not in 
						( SELECT * FROM 
							(	select gallery_id from t_gallery where active = 1 and flag_index = 1
													order by sort_order DESC limit 6
							) 
						as t)	limit 6	";
							
							//echo $sql;
				$objQuery = mysql_query($sql);	
				echo "<div class='item' style='padding: 0px 35px;'>";
					while ($row = mysql_fetch_array($objQuery)) {
						
						
							echo "<div class='col-md-2 col-sm-4 col-xs-6' style='height: 225px;'>
										
										<a href='gallery_detail.php?Id=".encode($row['gallery_id'])."'>
											<img class='img-responsive img-hover' src='".$row['images']."' alt='".$row['title']."'>
										</a>
										<h5><span class='th'>".$row['title']."</span>
										   <span class='en'>".$row['title_en']."</span></h5>
									  </div>";
					}
				echo"</div>";
			echo "</div>";
		
		?>
		
        <!--/carousel-inner--> <a class="left carousel-control" href="#myCarousel1" data-slide="prev" >‹</a>
		<a class="right carousel-control" href="#myCarousel1" data-slide="next" >›</a>
		</div>
                   
            
		</div>
<!-- Page End Gallery -->



<!-- Self Service --->
<div class="container">
<h2 class="page-header"><span class="th">บริการตัวเอง</span><span class="en">บริการตัวเอง</span></h2>
				<hr class='border' />
<?php 
				$sql = "SELECT * FROM t_article_cat where active = 1 ORDER BY sort_order ASC";
				$objQuery = mysql_query($sql);
				//echo $sql;
					while ($row = mysql_fetch_array($objQuery)) {
					echo "<div class='boxself'>
							<div class='article_box_index'>";
								
							echo "<a href='article_sub.php?Cat=".encode($row['cat_article_id'])."' alt='".$row['name']."'>";
							echo " <img src='".$row['icon']."'>
										</h4><span class='th' style='height: 60px;'>".$row['name']."</span>
										<span class='en' style='height: 60px;'>".$row['name_en']."</span> 
										
									</h4>
								</a>
								
							</div>
						</div>";
				}

?>
</div>
<!-- End Self Service --->






<!---- Knowledge ---->
<div class="container" style='margin-top: 100px;'>

<div class="col-lg-12">
<h2 class="page-header"><span class="th">คลังความรู้</span><span class="en">คลังความรู้</span></h2>
		<hr>

                 <ul id="myTab" class="nav nav-tabs nav-justified">
                    <li class="active"><a href="#service-one" data-toggle="tab">วีดีโอสื่อการเรียนรู้</a>
                    </li>
                    <?php 
						$sql = "SELECT * FROM t_km_cat
								WHERE active = 1 
								and flag_index = 1 
								ORDER BY sort_order ASC
								limit 5";
						$objQuery = mysql_query($sql);
						while ($row = mysql_fetch_array($objQuery)) {
							
							
							echo "<li class=''>";
							
							echo "	
									<a href='#service-".$row['cat_km_id']."' data-toggle='tab'>
										<span class='en'>".$row['name_en']."</span>
										<span class='th'>".$row['name']."</span>
									</a>
									
									
								</li>
							";
						}
					?>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="service-one">
						<?php 
							$sql = "SELECT * FROM t_vdo where active = 1 and flag_index = 1
									ORDER BY vdo_id DESC
									LIMIT 8";
					
							$objQuery = mysql_query($sql);
							//echo $sql;
							while ($row = mysql_fetch_array($objQuery)) {
								echo "<div class='col-md-6 col-sm-6'>";
								echo "	<div class='vdo_box_2'>
										<div class='col-md-5 col-sm-5'>
											<iframe width='100%' height='150' src='".$row['url']."' frameborder='0' allowfullscreen></iframe>
										</div>
										<div class='col-md-7 col-sm-7'>";
										
										echo "	<h5>
												<a href='vdo_detail.php?Id=".encode($row['vdo_id'])."'>
													<span class='th'>".$row['title']."</span> 
													<span class='en'>".$row['title_en']."</span>
												</a>
											</h5>
											<p>".$row['update_date']."</p>
											<p><span class='en'>ที่มา :</span><span class='th'>ที่มา :</span>
											   <span>".$row['url']."</span>
											<p>";
								
											
								echo	"</div>
										</div>";		
										
								echo "</div>";
							}

						?>
						
                       
						<p class="text-right"><a href="vdo.php"><span class="en">อ่านเพิ่มเติม</span><span class="th">อ่านเพิ่มเติม</span></a></p>
					</div>
					<?php 
						$sql = "SELECT * FROM t_km_cat
								WHERE active = 1 and flag_index = 1
								ORDER BY sort_order ASC
								limit 5";
						$objQuery = mysql_query($sql);
						while ($row = mysql_fetch_array($objQuery)) {
							
						
								echo "<div class='tab-pane fade' id='service-".$row['cat_km_id']."'>";
										$sql1 = "SELECT
													k1.km_id,
													k1.cat_km_id,
													k1.images,
													k1.title_en,
													k1.title,
													k1.content_en,
													k1.content,
													DATE_FORMAT(k1.update_date,'%d/%m/%Y') as lastDate,
													k1.sort_order,
													k1.active,
													k1.flag_index,
													k2.`name`,
													k2.path,
													k2.type_file
													FROM
													t_km AS k1
													LEFT JOIN t_km_file AS k2 ON k2.km_id = k1.km_id
													WHERE
													k1.active = 1 
													-- AND k1.flag_index = 1 
													and
													k1.cat_km_id = ".$row['cat_km_id']."
													group by k1.km_id
													ORDER BY k1.sort_order DESC
													LIMIT 10";
						
						
										$objQuery1 = mysql_query($sql1);
									
										while ($row1 = mysql_fetch_array($objQuery1)) {
										//echo $sql;
										echo "<div class='col-md-6 col-sm-6'>";
										echo "	<div class='km_box_2'>
												<div class='col-md-5 col-sm-5'>
													<img src='".$row1['images']."' style='width: 100%;' alt='".$row1['title']."'>
												</div>
												<div class='col-md-7 col-sm-7'>
													<h4><span class='th'>".$row1['title']."</span> <span class='en'>".$row1['title_en']."</span></h4>
													<p>".$row1['lastDate']."</p>
													<a href='km_detail.php?id=".encode($row1['km_id'])."' class='btn btn-default'>ข้อมูลเพิ่มเติม</a> 
										";
										if($row1['path'] == ""){
											echo "";
											
										}else{
											echo "<span class='en'><a href='".$row1['path']."'>Download</a></span>
												  <span class='th'><a href='".$row1['path']."' >ดาวน์โหลดเอกสาร</a></span>";
										}
										
													
										echo	"</div>
												</div>";		
												
										echo "</div>";
								
									}
								
								echo "</div>";
						}
					?>
					
		
   				</div>
            </div>
       
</div>
<!---- End Knowledge ---->



<!---- News ---->
<div class="container" style='margin-top: 100px;'>
<div class="col-lg-12">
<h2 class="page-header"><span class="th">ข่าวประชาสัมพันธ์</span><span class="en">ข่าวประชาสัมพันธ์</span></h2>
		<hr>
	
		<p class="text-right"><a href='news_list.php' style="float: right;margin-top: -50px;"><span class='th'>อ่านทั้งหมด >><span><span class='en'>Read more  >></span></a></p>

                <ul id="myTab" class="nav nav-tabs nav-justified">
				
					<?php 
						$sql = "SELECT * FROM t_news_cat
								WHERE active = 1 and flag_index = 1
								ORDER BY sort_order ASC";
						$objQuery = mysql_query($sql);
						while ($row = mysql_fetch_array($objQuery)) {
							
							if($row['cat_news_id'] == 1){
								echo "<li class='active'>";
								
							}else{
								echo "<li class=''>";
							}
							echo "	
									<a href='#News-".$row['cat_news_id']."' data-toggle='tab'>
										<span class='en'>".$row['name_en']."</span>
										<span class='th'>".$row['name']."</span>
									</a>
									
									
								</li>
							";
						}
					?>
                </ul>

                <div id="myTabContent" class="tab-content">
				
					<?php 
						$sql1 = "SELECT * FROM t_news_cat
								WHERE active = 1 and flag_index = 1
								ORDER BY sort_order ASC";
								
						$objQuery1 = mysql_query($sql1);
						while ($row1 = mysql_fetch_array($objQuery1)) {
						
							
							if($row1['cat_news_id'] == 1){
								echo "<div class='tab-pane fade active in' id='News-".$row1['cat_news_id']."'>";
								
							}else{
								echo "<div class='tab-pane fade' id='News-".$row1['cat_news_id']."'>";
							}
							echo "<table class='table table-striped'>";
								
								$sql = "SELECT news_id, cat_news_id,title, title_en,flag_index, DATE_FORMAT(update_date,'%d/%m/%Y') as update_date 
										FROM t_news
										WHERE cat_news_id = ".$row1['cat_news_id']." AND active = 1 and flag_index = 1
										ORDER BY sort_order DESC
										limit 10 ";
					
									//echo $sql;	
									$data  = mysql_query($sql);
									
									while ($row = mysql_fetch_array($data)) {
										
										echo "<tr>";
											echo "<td style='width: 100px;'>";
												echo "<span>".$row['update_date']."</span>";
											echo "</td>";
											echo "<td class='text-left'>";
												echo "<a href='news_detail.php?id=".encode($row['news_id'])."' ><span class='th'>".$row['title']."</span> <span class='en'>".$row['title_en']."</span></a>";
											echo "</td>";
										
										echo "</tr>";
									}
									
									echo "</table>";
									echo "";
							echo "</div>"; //จบ div tab
						}
					?>
				
                    
				</div>
				</div>
            </div>
</div>
<!---- End NEWS ---->

<!---- Calendar ---->
<div class="container">

                <h2 class="page-header"><span class="th">ปฏิทินกิจกรรม</span><span class="en">Calendar</span></h2>
				<hr class='border' />
				<p class="text-right">
					<a href="calendar.php" style="float: right;margin-top: -50px;">
					<span class="en">อ่านเพิ่มเติม</span>
					<span class="th">อ่านเพิ่มเติม</span>
					</a>
				</p>
				
			<div class="col-lg-7">
				<div id='calendar'></div>
			</div>
			<div class="col-md-5">
				<form name="frmSearch" id="frmSearch"    method="get" action="<?php  $_SERVER['SCRIPT_NAME'] ?> ">
					<div class="col-sm-4"><input type="text" id="startDate" name="startDate" value="" placeholder="วันที่เริ่มต้น" /></div>
					<div class="col-sm-4"><input type="text" id="endDate"  name="endDate" value="" placeholder="วันที่สิ้นสุด" /></div>
					<div class="col-sm-4"><input type="submit" value="ค้นหา"   /></div>
					
					
					<?php   
						if(!isset($_REQUEST["startDate"]) && empty($_REQUEST["startDate"])){
								$startDate = '';
						}else{
								$startDate = strip_tags(mysql_real_escape_string($_REQUEST["startDate"]));
						}

						if(!isset($_REQUEST["endDate"]) && empty($_REQUEST["endDate"])){
								$endDate = '';
						}else{
								$endDate = strip_tags(mysql_real_escape_string($_REQUEST["endDate"]));
						}
					?>
				</form>
				<div class="boxlistCar">
					
					<?php 
						$datemm = date("Y-m");
						$dateda =  date("Y-m-d" );
						$dateha = $dateda ." 00:00:00";
						//echo $datemm;
					
						$sql = " select event_id,title,title_en, DATE_FORMAT(`start`,'%d/%m/%Y') as `start` from t_event";
								 if($startDate == ""){
									 $sql .= " where `start` > '".$dateha."' ";
								 }else {
									 $sql .= " where `start` BETWEEN '".$startDate." 00:00:00' and '".$endDate." 23:59:59'";
								 }
							
							//echo $sql;
								$objQuery = mysql_query($sql);
								echo "<table style='width: 100%;'>";
								
									while ($row = mysql_fetch_array($objQuery)) {
										echo "
											<tr>
												<td>".$row['start']."</td>
												<td>
													<a href='event_detail.php?id=".$row['event_id']."'>
														<span class='th'>".$row['title']."</span>
												   		<span class='en'>".$row['title_en']."</span>
												   </a>
												</td>
												
												
											</tr>";
										
									}
							echo "</table>";
					?>
					</div>
			</div>
</div>
<!---- End Calendar ---->

<!---- People ---->
<div class="container">
<h2 class="page-header"><span class="th">บุคลากรดีเด่นด้านการเกษตร</span><span class="en">บุคลากรดีเด่นด้านการเกษตร</span></h2>
				<hr class='border' />
				<p class="text-right">
					<a href="people_cat.php" style="float: right;margin-top: -50px;">
					<span class="en">ดูทั้งหมด</span>
					<span class="th">ดูทั้งหมด</span>
					</a>
				</p>
           
           
                <div class="row">
					<div class="col-md-12">
					<div class='boxPeople'>
				<!-- begin panel group -->
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					
					<?php 
								$sql = "SELECT * FROM t_people_cat WHERE active = 1 ";
								
								$objQuery = mysql_query($sql);
									while ($row = mysql_fetch_array($objQuery)) {
										echo "<div class='panel panel-default'>
											<span class='side-tab' data-target='#tab".$row['cat_people_id']."' data-toggle='tab' role='tab' aria-expanded='false'>
												<div class='panel-heading' role='tab' id='headingOne'data-toggle='collapse' data-parent='#accordion' href='#collapse".$row['cat_people_id']."' aria-expanded='true' aria-controls='collapse".$row['cat_people_id']."'>
													<h4 class='panel-title'><i class='fa fa-chevron-down' aria-hidden='true'></i> <span class='en'>".$row['name_en']."</span><span class='th'>".$row['name']."</span>";
													
													
													
										echo"	</h4>
											</div>
									
											</span>";
											
											if($row['cat_people_id'] == 1){
												echo "<div id='collapse".$row['cat_people_id']."' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='heading".$row['cat_people_id']."'>";
											}else{
												echo "<div id='collapse".$row['cat_people_id']."' class='panel-collapse collapse' role='tabpanel' aria-labelledby='heading".$row['cat_people_id']."'>";
											}
											
											
											
										echo	"<div class='panel-body'>";
													$sql1 = "SELECT * FROM t_people where active = 1 and  ";
														  
													$sql1 .= "cat_people_id =".$row['cat_people_id'];
													$sql1 .= " limit 8";
													$objQuery1 = mysql_query($sql1);
													
													//echo $sql1;

													while ($row1 = mysql_fetch_array($objQuery1)) {
													
														echo "<div class='col-md-3 col-sm-3'>";
														echo "	<div class='people_box_2'>
																
																<div class='text-center'>
																<a href='people_detail.php?Id=".encode($row1['people_id'])."'>
																	<img class='img-responsive img-hover' alt='".$row1['title']."' src='".$row1['images']."' alt='' style='height: 250px;width: 100%'>
																</a> 
																<br clear='all'>
																<h5><span class='th'>".$row1['title']."</span>
																   <span class='en'>".$row1['title_en']."</span></h5>
																</div>
																
																";
													
														echo "</div>";
														echo "</div>";
													}
												
												echo "</div>
											</div>
										</div>";
										
									}
										
									
									
									

							?>
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>
<!---- End People ---->


<!---- ร้านค้ารัฐวิสาหกิจชุมชน ---->
<div class="container">
<h2 class="page-header"><span class="th">สินค้าวิสาหกิจชุมชน</span><span class="en">สินค้าวิสาหกิจชุมชน</span></h2>
				<hr class='border' />
				<p class="text-right">
					<a href="product.php" style="float: right;margin-top: -50px;">
					<span class="en">ดูทั้งหมด</span>
					<span class="th">ดูทั้งหมด</span>
					</a>
				</p>
            
           
                
					<?php 
					$sql = "select * from t_product where active = 1 
							order by product_id DESC limit 12 ";
					//echo $sql;
					$objQuery = mysql_query($sql);
						
							while ($row = mysql_fetch_array($objQuery)) {
								echo "<div class='col-md-2'>
										<div class='text-center'>
										<a href='product_detail.php?Id=".encode($row['product_id'])."'>
											<img class='img-responsive img-hover' alt='".$row['title']."' src='".$row['images']."' alt='' style='height: 150px;width: 100%'>
										</a>
										<h5><span class='th'>".$row['title']."</span>
										   <span class='en'>".$row['title_en']."</span></h5>
										</div>
									  </div>";
							}
				
				
				?>
					
        </div>
</div>
<!--- End รัฐวิสาหกิจชุมชน ------>

<!-- service -->
<div class="container">
            <div class="col-lg-12">
				<h2 class="page-header"><span class="th">ลิงก์ที่น่าสนใจ</span><span class="en">ลิงก์ที่น่าสนใจ</span></h2>
				<hr class='border' />
				<p class="text-right">
					<a href="service.php" style="float: right;margin-top: -50px;">
					<span class="en">ดูลิงก์ที่น่าสนใจเพิ่มเติม</span>
					<span class="th">ดูลิงก์ที่น่าสนใจเพิ่มเติม</span>
					</a>
				</p>
            </div>
           
          
				<div class="row">
				<?php
					$sql1 = "SELECT * FROM t_link_file where active = 1 order by file_id limit 6 ";			
					$objQuery1 = mysql_query($sql1);
					
					//echo $sql1;

									while ($row1 = mysql_fetch_array($objQuery1)) {
										echo "<div class='col-md-2 col-sm-3'>";
										echo "	<div class='sevice_box_2'>
												<a href='".$row1['url']."' title='".$row1['title']."' target='_blank'>
												<img src='".$row1['path']."' style='width: 100%;' >
												</a>";
										
									echo "</div>";
									echo "</div>";
								}
					?>
				
			</div>
				
		
</div>
<!---- End Service ---->
	
	
	
<input type="hidden" name="user_id" id="user_id" value="<?=$_SESSION['userid'] ?>">
<input type="hidden" name="ID" id="ID" value="0"> 	
<?php  include('footer.php') ?>
     
<script type="text/javascript" src="js/fullcalendar-2.1.1/lib/moment.min.js"></script>  
<script type="text/javascript" src="js/fullcalendar-2.1.1/fullcalendar.min.js"></script>  
<script type="text/javascript" src="js/fullcalendar-2.1.1/lang/th.js"></script>  
<script type="text/javascript" src="script.js"></script>
<link href='css/fullcalendar.css' rel='stylesheet' />
<link href='css/fullcalendar.print.css' rel='stylesheet' media='print' />

		<link rel="stylesheet" media="all" type="text/css" href="calendar/jquery-ui.css" />
		<link rel="stylesheet" media="all" type="text/css" href="calendar/jquery-ui-timepicker-addon.css" />
		<script type="text/javascript" src="calendar/jquery-ui.min.js"></script>
		<script type="text/javascript" src="calendar/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="calendar/jquery-ui-sliderAccess.js"></script>
		

<script type="text/javascript">
function cmdHiddenKM(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_km.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}

function cmdDelKM(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_km.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}




function cmdHiddenP(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_pest.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelP(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_pest.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}



function cmdHiddenA(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_cat_article.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdHiddenN(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_news.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	

}
function cmdDelN(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_news.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}



function cmdHiddenB(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_banner.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
function cmdShowB(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/show_banner.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกแสดงผลเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelB(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_banner.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
////////////////////// Gallery

function cmdHiddenG(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_gallery.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
function cmdShowG(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/show_gallery.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกแสดงผลเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelG(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_gallery.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}

function cmdHiddenPP(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_people.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
function cmdShowPP(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/show_people.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกแสดงผลเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelPP(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_people.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}



function cmdHiddenFF(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_service.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
function cmdShowFF(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/show_service.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกแสดงผลเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelFF(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_service.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}

function cmdHiddenPR(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/hidden_product.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกซ่อนเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}
function cmdShowPR(ID){
	//alert(ID);
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/show_product.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ถูกแสดงผลเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}


function cmdDelPR(ID){
	//alert(ID);
	if(confirm('  กรุณายืนยันการลบอีกครั้ง !!!  ')){
		//return true;
	}else{
		return false;
	}
	
	document.getElementById('ID').value = ID;
	var params = "n=" + Math.random();

    params = params + "&" + $('#ID').serialize();
	params = params + "&" + $('#user_id').serialize();

	

	$.ajax({
		type: "POST",
		url: "function/del_product.php",
		data: params,
		success: function(sxAjax){			
			if (sxAjax.substr(0,9) == "Completed") {
				alert('ลบเรียบร้อยแล้ว')
                location.reload();

			} else {
				alert(sxAjax);
			}	
		}
	});	


}

$(function(){  
  
    $('#calendar').fullCalendar({  
        header: {  
            left: 'prev,next today',    
            center: 'title',  
            right: 'month,agendaWeek,agendaDay',  
        },    
        events: {  
            url: 'data_events.php?gData=1',  
            error: function() {  
  
            }  
        },      
        eventLimit:true,  
        lang: 'th'  
    });  
       
}); 
	$(function(){

	$("#startDate").datepicker({
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
	});
	$("#endDate").datepicker({
		dateFormat: 'yy-mm-dd',
		numberOfMonths: 1,
	});

});

</script>




