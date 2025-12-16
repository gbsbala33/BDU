<?php

include("includes/session_include.php");
include("includes/enc_conn_include.php");
include("includes/header_validate_code.php");


if (!isset($_SESSION["category"]) || $_SESSION['category'] != "CO") {
?>
	<script>
		alert('<?php echo "Invalid Access"; ?>');
		window.location = "logout.php";
	</script>
<?php
	exit;
}


// SELECT CONVOCATION ID 
$selConvocationId = "SELECT DISTINCT convocation_id FROM convocation.trn_student_convocation_profile
WHERE delete_flag = '0' ORDER BY convocation_id DESC";
// Set the PDO error mode to exception
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	$exeConvocationId = $db->prepare($selConvocationId);
	$exeConvocationId->execute();

	$exeConvocationIdModify = $db->prepare($selConvocationId);
	$exeConvocationIdModify->execute();
} catch (PDOException $e) {
	//Do your error handling here
	$message = $e->getMessage();
	echo "POD ERROR 1 <br>" . $message . "<br><br>";
}







// SELECT COLLEGE TYPE FROM INSTITUTION MASTER
$selCollegeType = "SELECT a.college_type, b.college_type_name FROM convocation.mst_convocation_institution a
LEFT JOIN 
convocation.mst_college_type b ON a.college_type = b.college_type
GROUP BY a.college_type, b.college_type, b.college_type_name ORDER BY a.college_type";
// Set the PDO error mode to exception
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	$exeCollegeType = $db->prepare($selCollegeType);
	$exeCollegeType->execute();
} catch (PDOException $e) {
	//Do your error handling here
	$message = $e->getMessage();
	echo "POD ERROR 1 <br>" . $message . "<br><br>";
}



// SELECT CERTIFICATE TYPE DETAILS
$selCertifiTypeDt = "SELECT certificate_id, certificate_type, certificate_short_code
FROM convocation.mst_certificate_type  ORDER BY order_by ASC";

// Set the PDO error mode to exception
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
	$exeCertifiTypeDt = $db->prepare($selCertifiTypeDt);
	$exeCertifiTypeDt->execute();
} catch (PDOException $e) {
	//Do your error handling here
	$message = $e->getMessage();
	echo "POD ERROR 1 <br>" . $message . "<br><br>";
}


?>

<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Bharathidasan University, Tiruchirappalli, Tamil Nadu, India.</title>
	<!-- Favicon -->
	<link rel="shortcut icon" href="images/favicon.ico" />
	<style>
		label {
			font-size: 13px;
		}

		sup {
			color: red !important;
		}

		/* Align modal header elements on the same line using flexbox */
		.modal-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 1rem;
		}

		.modal-header .close {
			font-size: 2.5rem;
		}

		.star-symbol {
			color: red;
			font-size: 0.8rem;
			vertical-align: top;
			margin-left: 2px;
		}
	</style>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<div id="loaderId" class="mainLoader" style="display:none">
		<div class="preloader" id="preLoaderId">
			<div class="innercircle">
				<h4 style="margin-top: 20px;">BDU</h4>
				NIC
			</div>
		</div>
	</div>
</head>

<body>
	<link href="datePicker/jquery_ui_1_10_4.css" rel="stylesheet">
	<script src="datePicker/jquery-ui.min.js"></script>
	<script src="datePicker/jquery_1_10_2.js"></script>

	<?php include("header.php"); ?>

	<script src="datePicker/jquery_ui_1_10_4.js"></script>

	<!--	DOB DIV =====  DOB CORRECTION	-->
	<style>
		.ui-widget-header {
			border: 1px solid #006899;
			background: none;
			background-color: #006899;
			color: #000000;
		}

		.top-header {
			border: 1px solid;
			background-color: #006899;
			color: #FFF;
			font-weight: bold;
			padding: 3px;
			text-align: center;
		}

		.level-active:hover {
			background: #006899;
			color: #fff;
		}
	</style>

	<div class="contact-w3-agileits master" id="contact" style="padding: 0px 0px;">
		<div class="container">
			<h3 class="heading_style" style="font-size: 15px;">Convocation - Certificate Profile <span class="col-md-12" style="text-align:right; "> </span></h3>
		</div>
	</div>
	<div class="container well">
		<div class="contact-w3-agileits master" id="contact" style="padding: 0px 0px;">
			<form id="sel_stud" name="sel_stud" action="coe_convocation_profile_report.php" method="post" enctype="multipart/form-data">
				<input type="hidden" id="pagetoken" name="pagetoken" value="<?php echo $_SESSION["pagetoken"]; ?>">
				<div class="col-md-12 textbox_width" id="display_degree">
					<div class="col-md-4">
						<label class="col-md-5 control-label" for="convocation_id">Convocation<sup>*</sup></label>
						<select class="select_box_pad_reg" name="convocation_id" id="convocation_id">
							<option value="">Select Convocation ID</option>
							<?php
							if ($exeConvocationId->rowCount() > 0) {
								while ($resConvocationId = $exeConvocationId->fetch(PDO::FETCH_ASSOC)) {
							?>
									<option value="<?php echo $resConvocationId['convocation_id'] ?>"><?php echo $resConvocationId['convocation_id']; ?> </option>
							<?php
								}
							}
							?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="col-md-5 control-label" for="certificate_type">Certificate&nbsp;Type<sup>*</sup></label>
						<select class="select_box_pad_reg" name="certificate_type" id="certificate_type">
							<option value="">Select Certificate Type</option>
							<?php while ($resCertifiTypeDt = $exeCertifiTypeDt->fetch(PDO::FETCH_ASSOC)) { ?>
								<option value="<?php echo $resCertifiTypeDt['certificate_id']; ?>">
									<?php echo $resCertifiTypeDt['certificate_type']; ?> </option>
							<?php } ?>
						</select>
					</div>

					<div class="col-md-4">
						<label class="col-md-5 control-label" for="college_type">College&nbsp;Type<sup>*</sup></label>
						<select class="select_box_pad_reg" name="college_type" id="college_type">
							<option value="">Select College Type</option>
							<?php
							if ($exeCollegeType->rowCount() > 0) {
								while ($resCollegeType = $exeCollegeType->fetch(PDO::FETCH_ASSOC)) {
									$colType = "";
									$collegeType = $resCollegeType["college_type"];
									$colType = $resCollegeType["college_type_name"];
							?>
									<option value="<?php echo $collegeType; ?>"><?php echo $colType; ?> </option>
							<?php
								}
							}
							?>
						</select>
					</div>

				</div>

				<div class="col-md-12 textbox_width">

					<div class="col-md-6">
						<label class="col-md-5 control-label" for="institution_code">Institution<sup>*</sup></label>
						<select class="select_box_pad_reg" name="institution_code" id="institution_code">
							<option value="">Select Institution</option>
						</select>
					</div>

					<div class="col-md-3">
						<label class="col-md-5 control-label" for="degree">Degree<sup>*</sup></label>
						<select class="select_box_pad_reg" name="degree" id="degree">
							<option value="">Select Degree</option>
						</select>
					</div>

					<div class="col-md-3">
						<label class="col-md-5 control-label" for="course">Discipline<sup>*</sup></label>
						<select class="select_box_pad_reg" name="course" id="course" onchange="getStudentProfile();">
							<option value="">Select Discipline</option>
						</select>
					</div>

				</div>
			</form>
		</div>
	</div>

	<div class="contact-w3-agileits master" name="student_profile_details" id="student_profile_details"></div>

	<!-- Edit Modal -->
	<div class="modal fade" id="popupBox" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: whitesmoke;border-radius: inherit;">
					<h2 class="modal-title" id="editModalLabel" style="color:black;">MODIFY STUDENT DETAILS</h2>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" style="font-weight: bold;">
					<form id="editForm">
						<input type="hidden" id="convocationId_j" name="convocationId_j">
						<input type="hidden" id="institutionCode_j" name="institutinstitutionCode_jion_code">
						<input type="hidden" id="row_id_j" name="row_id_j">
						<input type="hidden" id="univ_reg_no_j" name="univ_reg_no_j">
						<input type="hidden" id="collegetype_j" name="collegetype_j">
						<input type="hidden" id="degreeId_j" name="degreeId_j">
						<input type="hidden" id="courseId_j" name="courseId_j">
						<div class="form-group">
							<label for="conv_id_j" style="font-weight: bold;">Conv. Id<span class="star-symbol">★</span></label>
							<select class="form-control" name="conv_id_j" id="conv_id_j">
								<option value="">Select Convocation ID</option>
								<?php
								if ($exeConvocationIdModify->rowCount() > 0) {
									while ($resConvocationIdModify = $exeConvocationIdModify->fetch(PDO::FETCH_ASSOC)) {
								?>
										<option value="<?php echo $resConvocationIdModify['convocation_id'] ?>"><?php echo $resConvocationIdModify['convocation_id']; ?> </option>
								<?php
									}
								}
								?>
							</select>
						</div>

						<div class="form-group">
							<label for="student_name_en_j" style="font-weight: bold;">Student Name En<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="student_name_en_j" name="student_name_en_j">
						</div>

						<div class="form-group">
							<label for="student_name_ta_j" style="font-weight: bold;">Student Name Ta<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="student_name_ta_j" name="student_name_ta_j">
						</div>

						<div class="form-group">
							<label for="convocation_year_j" style="font-weight: bold;">conv. Year<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="convocation_year_j" name="convocation_year_j">
						</div>

						<div class="form-group">
							<label for="convocation_month_j" style="font-weight: bold;">conv. Month<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="convocation_month_j" name="convocation_month_j">
						</div>

						<div class="form-group">
							<label for="total_marks_j" style="font-weight: bold;">Total Marks<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="total_marks_j" name="total_marks_j">
						</div>

						<div class="form-group">
							<label for="code_no_j" style="font-weight: bold;">Code No<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="code_no_j" name="code_no_j">
						</div>

						<div class="form-group">
							<label for="serial_no_j" style="font-weight: bold;">Serial No<span class="star-symbol">★</span></label>
							<input type="text" class="form-control" id="serial_no_j" name="serial_no_j">
						</div>

						<div class="form-group">
							<label for="grade_name_en_j" style="font-weight: bold;">Grade Name En</label>
							<input type="text" class="form-control" id="grade_name_en_j" name="grade_name_en_j">
						</div>

						<div class="form-group">
							<label for="title_of_the_thesis_j" style="font-weight: bold;">Title of Thesis</label>
							<input type="text" class="form-control" id="title_of_the_thesis_j" name="title_of_the_thesis_j">
						</div>

					</form>
				</div>
				<div class="modal-footer" style="background-color: whitesmoke;border-radius: inherit;">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<input type="button" class="btn btn-primary" id="saveBtn" value="SUBMIT" onclick="return editModifyStudentDetails();">
				</div>
			</div>
		</div>
	</div>


	<!-- FOOTER -->
	<?php include("footer.php"); ?>
	<!-- FOOTER END	-->


	<script type="text/javascript">
		/*	SCRIPT FOR LOAD EMPTY THE NEXT ALL VALUES	*/
		$(document).ready(function() {
			$('#certificate_type').on('change', function() {
				$("#student_profile_details").hide();
				$('#college_type').val("");
				$('#institution_code').val("");
				$('#degree').val("");
				$('#course').val("");
			});
		});



		/*	SCRIPT FOR LOAD INSTITUTION DETAILS	*/
		$(document).ready(function() {
			$("#college_type").change(function() {
				var convocation_id = $('#convocation_id').val();
				var certificate_type = $('#certificate_type').val();
				var convocation_date = $('#convocation_date').val();
				var college_type = $('#college_type').val();

				var passdegree = '<option value="">Select Degree</option>';
				var passcourse = '<option value="">Select Discipline</option>';
				var type = 'getInstitutionDetails';

				if (convocation_id == "") {
					var msg = 'Select Convocation';
					$('#convocation_id').focus();
					$('#convocation_id').css('border-color', 'red');
					$('#convocation_id').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_id').css('box-shadow', '');
					$('#convocation_id').css('border-color', '');
				}

				if (convocation_date == "") {
					var msg = 'Select Convocation Date.';
					$('#convocation_date').focus();
					$('#convocation_date').css('border-color', 'red');
					$('#convocation_date').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_date').css('box-shadow', '');
					$('#convocation_date').css('border-color', '');
				}

				if (certificate_type == "") {
					var msg = 'Select Certificate Type';
					$('#certificate_type').focus();
					$('#certificate_type').css('border-color', 'red');
					$('#certificate_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#certificate_type').css('box-shadow', '');
					$('#certificate_type').css('border-color', '');
				}

				if (college_type == "") {
					var msg = 'Select College Type';
					$('#college_type').focus();
					$('#college_type').css('border-color', 'red');
					$('#college_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#college_type').css('box-shadow', '');
					$('#college_type').css('border-color', '');
				}

				$("#loaderId").show();
				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_convocation.php",
					data: {
						convocation_id: convocation_id,
						certificate_type: certificate_type,
						college_type: college_type,
						type: type
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data); console.log(data); */ ?>

						$('#loaderId').delay(200).fadeOut('slow');

						if (data == 0) {
							$('#convocation_id').val("");
							$('#certificate_type').val("");
							$('#print_type').val("");
							$('#college_type').val("");
							$('#institution_code').val("");
							$('#degree').val("");
							$('#course').val("");
							var msg = 'No Records Found.!';
							message_error(msg);
							return false;
						} else if (data == 3) {
							$('#degree').val("");
							$('#course').val("");
							var msg = 'Some data missing. Please logout and try again.!';
							message_error(msg);
							return false;
						}

						$('#student_profile_details').hide();
						$("#institution_code").html(data);
						$("#degree").html(passdegree);
						$("#course").html(passcourse);
					}
				});
			});
		});


		// AFTER DISPLAY THE STUDENT DETAILS CHANGE THE INSTITUION CODE MEANS NEED TO CHANGE THE DEGREE AND COURSE DETAILS.
		$(document).ready(function() {
			$('#institution_code').on('change', function() {
				$("#student_profile_details").hide();
				$('#degree').val("");
				$('#course').val("");
			});
		});


		/*	SCRIPT FOR LOAD DEGREE	*/
		$(document).ready(function() {
			$("#institution_code").change(function() {
				var convocation_id = $('#convocation_id').val();
				var certificate_type = $('#certificate_type').val();
				var convocation_date = $('#convocation_date').val();
				var college_type = $('#college_type').val();
				var instDt = $('#institution_code').val();
				var instRes = instDt.split("$$$");
				var row_id = instRes['0'];
				var institution_code = instRes['1'];
				var passdata = '<option value="">Select Discipline</option>';
				var type = 'getDegreeDetails';

				if (convocation_id == "") {
					var msg = 'Select Convocation';
					$('#convocation_id').focus();
					$('#convocation_id').css('border-color', 'red');
					$('#convocation_id').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_id').css('box-shadow', '');
					$('#convocation_id').css('border-color', '');
				}

				if (convocation_date == "") {
					var msg = 'Select Convocation Date.';
					$('#convocation_date').focus();
					$('#convocation_date').css('border-color', 'red');
					$('#convocation_date').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_date').css('box-shadow', '');
					$('#convocation_date').css('border-color', '');
				}

				if (certificate_type == "") {
					var msg = 'Select Certificate Type';
					$('#certificate_type').focus();
					$('#certificate_type').css('border-color', 'red');
					$('#certificate_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#certificate_type').css('box-shadow', '');
					$('#certificate_type').css('border-color', '');
				}

				if (college_type == "") {
					var msg = 'Select College Type';
					$('#college_type').focus();
					$('#college_type').css('border-color', 'red');
					$('#college_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#college_type').css('box-shadow', '');
					$('#college_type').css('border-color', '');
				}


				$("#loaderId").show();
				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_convocation.php",
					data: {
						convocation_id: convocation_id,
						certificate_type: certificate_type,
						institution_code: institution_code,
						type: type
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data); console.log(data); */ ?>

						$('#loaderId').delay(200).fadeOut('slow');

						if (data == 0) {
							$('#convocation_id').val("");
							$('#certificate_type').val("");
							$('#print_type').val("");
							$('#college_type').val("");
							$('#institution_code').val("");
							$('#degree').val("");
							$('#course').val("");
							var msg = 'No Records Found.!';
							message_error(msg);
							return false;
						} else if (data == 3) {
							$('#degree').val("");
							$('#course').val("");
							var msg = 'Some data missing. Please logout and try again.!';
							message_error(msg);
							return false;
						}

						$('#student_profile_details').hide();
						$("#degree").html(data);
						//select_course_details(convocation_id,certificate_type,institution_code);
						$("#course").html(passdata);
					}
				});
			});
		});


		<?PHP /* // function select_course_details(convocation_id,certificate_type,institution_code){*/ ?>

		$(document).ready(function() {
			$("#degree").change(function() {

				var convocation_id = $('#convocation_id').val();
				var certificate_type = $('#certificate_type').val();
				var convocation_date = $('#convocation_date').val();
				var college_type = $('#college_type').val();
				var instDt = $('#institution_code').val();
				var instRes = instDt.split("$$$");
				var row_id = instRes['0'];
				var institution_code = instRes['1'];
				var degree_id = $('#degree').val();
				var passdata = '<option value="">Select Discipline</option>';
				var type = 'getCourseDetails';

				if (convocation_id == "") {
					var msg = 'Select Convocation';
					$('#convocation_id').focus();
					$('#convocation_id').css('border-color', 'red');
					$('#convocation_id').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_id').css('box-shadow', '');
					$('#convocation_id').css('border-color', '');
				}

				if (convocation_date == "") {
					var msg = 'Select Convocation Date.';
					$('#convocation_date').focus();
					$('#convocation_date').css('border-color', 'red');
					$('#convocation_date').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#convocation_date').css('box-shadow', '');
					$('#convocation_date').css('border-color', '');
				}


				if (certificate_type == "") {
					var msg = 'Select Certificate Type';
					$('#certificate_type').focus();
					$('#certificate_type').css('border-color', 'red');
					$('#certificate_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#certificate_type').css('box-shadow', '');
					$('#certificate_type').css('border-color', '');
				}

				if (college_type == "") {
					var msg = 'Select College Type';
					$('#college_type').focus();
					$('#college_type').css('border-color', 'red');
					$('#college_type').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#college_type').css('box-shadow', '');
					$('#college_type').css('border-color', '');
				}

				if (institution_code == "") {
					var msg = 'Select Institution code.';
					$('#institution_code').focus();
					$('#institution_code').css('border-color', 'red');
					$('#institution_code').css('box-shadow', '0 0 0.15rem crimson');
					message_error(msg);
					return false;
				} else {
					$('#institution_code').css('box-shadow', '');
					$('#institution_code').css('border-color', '');
				}

				<?PHP /* alert(convocation_id);
				alert(certificate_type);
				alert(institution_code);
				alert(degree_id);
				*/ ?>

				$("#loaderId").show();
				$.ajax({
					type: "POST",
					url: "ajax_get_degree_course_master_convocation.php",
					data: {
						convocation_id: convocation_id,
						certificate_type: certificate_type,
						institution_code: institution_code,
						degree_id: degree_id,
						type: type
					},
					cache: false,
					success: function(data) {
						<?php /* alert(data); console.log(data); */ ?>

						$('#loaderId').delay(200).fadeOut('slow');

						if (data == 0) {
							$('#course').val("");
							var msg = 'No Records Found.!';
							message_error(msg);
							return false;
						} else if (data == 3) {
							$('#course').val("");
							var msg = 'Some data missing. Please logout and try again.!';
							message_error(msg);
							return false;
						}

						$('#student_profile_details').hide();
						$("#course").html(data);
					}
				});
			});
		});


		function getStudentProfile() {
			var pagetoken = $("#pagetoken").val();
			var category = '<?php echo $_SESSION['category']; ?>';

			var convocationId = $('#convocation_id').val();
			var certificateType = $('#certificate_type').val();
			var convocation_date = $('#convocation_date').val();
			var collegetype = $('#college_type').val();
			var instDt = $('#institution_code').val();
			var instRes = instDt.split("$$$");
			var row_id = instRes['0'];
			var institutionCode = instRes['1'];
			var degreeId = $('#degree').val();
			var courseId = $("#course").val();
			//var institutionType = $("#institution_type").val();
			var type = 'getConvStudDtType';
			var mode = 'getConvStudDtMode';

			$("#loaderId").show();

			$.ajax({
				type: "POST",
				url: "coe_convocation_profile_correction_ajax.php",
				data: {
					pagetoken: pagetoken,
					category: category,
					convocationId: convocationId,
					certificateType: certificateType,
					collegetype: collegetype,
					row_id: row_id,
					institutionCode: institutionCode,
					degreeId: degreeId,
					courseId: courseId,
					type: type,
					mode: mode
				},
				cache: false,
				success: function(response) {
					$('#loaderId').delay(200).fadeOut('slow');

					<?php /* 
					alert(response);
					console.log(response);
					*/ ?>

					<?php /* alert(response); console.log(response); */ ?>

					if (response == 3) {
						$('#course').val("");
						var msg = 'Some data missing. Please logout and try again.!';
						message_error(msg);
						return false;
					} else if (response == 4) {

						$('#student_profile_details').hide();
						$('#course').val("");
						var msg = 'No records found. Based on the search option.';
						message_error(msg);
						return false;
					}

					$('#student_profile_details').show();
					$('#student_profile_details').html(response);
				}
			});
		}


		function modifyStudentDetails(convocationId, institutionCode, row_id, univ_reg_no, collegetype, degreeId, courseId) {

			var pagetoken = '<?php echo $_SESSION['pagetoken']; ?>';
			var category = '<?php echo $_SESSION['category']; ?>';
			var convocationId = convocationId;
			var institutionCode = institutionCode;
			var row_id = row_id;
			var univ_reg_no = univ_reg_no;
			var collegetype = collegetype;
			var degreeId = degreeId;
			var courseId = courseId;
			var type = 'modifyStudentMasterDetails';
			var mode = 'modifyStudentMasterDetailsMode';

			/*
			alert(convocationId);
			alert(institutionCode);
			alert(row_id);
			alert(univ_reg_no);
			alert(collegetype);
			alert(degreeId);
			alert(courseId);
			return false;
			*/

			$("#loaderId").show();

			$.ajax({
				type: "POST",
				url: "coe_convocation_profile_correction_ajax.php",
				data: {
					pagetoken: pagetoken,
					category: category,
					convocationId: convocationId,
					institutionCode: institutionCode,
					row_id: row_id,
					univ_reg_no: univ_reg_no,
					collegetype: collegetype,
					degreeId: degreeId,
					courseId: courseId,
					type: type,
					mode: mode
				},
				cache: false,
				success: function(response) {
					$('#loaderId').delay(200).fadeOut('slow');

					<?php /* alert(response); console.log(response); */ ?>

					if (response == 3) {
						$('#course').val("");
						var msg = 'Some data missing. Please logout and try again.!';
						message_error(msg);
						return false;
					} else if (response == 4) {
						var msg = 'No records found. Based on the search option.';
						message_error(msg);
						return false;
					}


					var jsonData = JSON.parse(response);
					// Now you can access properties of the JSON object, for example:
					console.log(jsonData.status);
					if (jsonData.status === 'success') {
						// Process successful data	

						//$('#convocationId_j').val(convocationId).trigger("change");

						$("#conv_id_j").val(convocationId);
						$("#institutionCode_j").val(institutionCode);
						$("#row_id_j").val(row_id);
						$("#univ_reg_no_j").val(univ_reg_no);
						$("#collegetype_j").val(collegetype);
						$("#degreeId_j").val(degreeId);
						$("#courseId_j").val(courseId);

						$("#student_name_en_j").val(jsonData.student_name_en);
						$("#student_name_ta_j").val(jsonData.student_name_ta);
						$("#convocation_year_j").val(jsonData.convocation_year);
						$("#convocation_month_j").val(jsonData.convocation_month);
						$("#total_marks_j").val(jsonData.total_marks);
						$("#code_no_j").val(jsonData.code_no);
						$("#serial_no_j").val(jsonData.serial_no);
						$("#grade_name_en_j").val(jsonData.grade_name_en);
						$("#grade_name_ta_j").val(jsonData.grade_name_ta);
						$("#cla_pra_en_j").val(jsonData.cla_pra_en);
						$("#auto_non_auto_j").val(jsonData.auto_non_auto);
						$("#distance_ft_pt_j").val(jsonData.distance_ft_pt);
						$("#title_of_the_thesis_j").val(jsonData.title_of_the_thesis);
						$("#certificate_type_j").val(jsonData.certificate_type);
						// Show the modal
						$('#popupBox').modal('show');

					} else {
						message_error(jsonData.error_message || 'An error occurred.');
					}
				}
			});
		}


		function editModifyStudentDetails() {

			var pagetoken = '<?php echo $_SESSION['pagetoken']; ?>';
			var category = '<?php echo $_SESSION['category']; ?>';
			var institutionCode_j = $('#institutionCode_j').val();
			var row_id_j = $('#row_id_j').val();
			var univ_reg_no_j = $('#univ_reg_no_j').val();
			var collegetype_j = $('#collegetype_j').val();
			var degreeId_j = $('#degreeId_j').val();
			var courseId_j = $('#courseId_j').val();
			var conv_id_j = $('#conv_id_j').val();
			var student_name_en_j = $('#student_name_en_j').val();
			var student_name_ta_j = $('#student_name_ta_j').val();
			var convocation_year_j = $('#convocation_year_j').val();
			var convocation_month_j = $('#convocation_month_j').val();
			var total_marks_j = $('#total_marks_j').val();
			var code_no_j = $('#code_no_j').val();
			var serial_no_j = $('#serial_no_j').val();
			var grade_name_en_j = $('#grade_name_en_j').val();
			var title_of_the_thesis_j = $('#title_of_the_thesis_j').val();
			var type = 'changeStudentConvDetails';
			var purpose = 'modifyStudentConvDetails';


			/* alert(institutionCode_j);
			alert(row_id_j);
			alert(univ_reg_no_j);
			alert(collegetype_j);
			alert(degreeId_j);
			alert(courseId_j);
			alert(conv_id_j);
			alert(student_name_en_j);
			alert(student_name_ta_j);
			alert(convocation_year_j);
			alert(convocation_month_j);
			alert(total_marks_j);
			alert(code_no_j);
			alert(serial_no_j);
			alert(grade_name_en_j);
			alert(title_of_the_thesis_j);

			return false; */

			if (institutionCode_j == "") {
				var msg = 'Institution code is missing. Refresh the page and try again.!';
				$('#institutionCode_j').focus();
				$('#institutionCode_j').css('border-color', 'red');
				$('#institutionCode_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#institutionCode_j').css('box-shadow', '');
				$('#institutionCode_j').css('border-color', '');
			}

			if (row_id_j == "") {
				var msg = 'Institution code is missing. Refresh the page and try again..!';
				$('#row_id_j').focus();
				$('#row_id_j').css('border-color', 'red');
				$('#row_id_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#row_id_j').css('box-shadow', '');
				$('#row_id_j').css('border-color', '');
			}

			if (univ_reg_no_j == "") {
				var msg = 'Student Registration number is missing. Refresh the page and try again.!';
				$('#univ_reg_no_j').focus();
				$('#univ_reg_no_j').css('border-color', 'red');
				$('#univ_reg_no_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#univ_reg_no_j').css('box-shadow', '');
				$('#univ_reg_no_j').css('border-color', '');
			}

			if (collegetype_j == "") {
				var msg = 'College type  is missing. Refresh the page and try again.!';
				$('#collegetype_j').focus();
				$('#collegetype_j').css('border-color', 'red');
				$('#collegetype_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#collegetype_j').css('box-shadow', '');
				$('#collegetype_j').css('border-color', '');
			}

			if (degreeId_j == "") {
				var msg = 'Degree code  is missing. Refresh the page and try again.!';
				$('#degreeId_j').focus();
				$('#degreeId_j').css('border-color', 'red');
				$('#degreeId_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#degreeId_j').css('box-shadow', '');
				$('#degreeId_j').css('border-color', '');
			}

			if (courseId_j == "") {
				var msg = 'Course code  is missing. Refresh the page and try again.!';
				$('#courseId_j').focus();
				$('#courseId_j').css('border-color', 'red');
				$('#courseId_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#courseId_j').css('box-shadow', '');
				$('#courseId_j').css('border-color', '');
			}

			if (conv_id_j == "") {
				var msg = 'Select Convocation';
				$('#conv_id_j').focus();
				$('#conv_id_j').css('border-color', 'red');
				$('#conv_id_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#conv_id_j').css('box-shadow', '');
				$('#conv_id_j').css('border-color', '');
			}

			if (student_name_en_j == "") {
				var msg = 'Enter student name';
				$('#student_name_en_j').focus();
				$('#student_name_en_j').css('border-color', 'red');
				$('#student_name_en_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#student_name_en_j').css('box-shadow', '');
				$('#student_name_en_j').css('border-color', '');
			}

			if (student_name_ta_j == "") {
				var msg = 'Enter student name';
				$('#student_name_ta_j').focus();
				$('#student_name_ta_j').css('border-color', 'red');
				$('#student_name_ta_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#student_name_ta_j').css('box-shadow', '');
				$('#student_name_ta_j').css('border-color', '');
			}

			if (convocation_year_j == "") {
				var msg = 'Enter convocation year';
				$('#convocation_year_j').focus();
				$('#convocation_year_j').css('border-color', 'red');
				$('#convocation_year_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#convocation_year_j').css('box-shadow', '');
				$('#convocation_year_j').css('border-color', '');
			}

			if (convocation_month_j == "") {
				var msg = 'Enter convocation month';
				$('#convocation_month_j').focus();
				$('#convocation_month_j').css('border-color', 'red');
				$('#convocation_month_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#convocation_month_j').css('box-shadow', '');
				$('#convocation_month_j').css('border-color', '');
			}

			if (total_marks_j == "") {
				var msg = 'Enter total marks.!';
				$('#total_marks_j').focus();
				$('#total_marks_j').css('border-color', 'red');
				$('#total_marks_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#total_marks_j').css('box-shadow', '');
				$('#total_marks_j').css('border-color', '');
			}

			if (code_no_j == "") {
				var msg = 'Enter code number';
				$('#code_no_j').focus();
				$('#code_no_j').css('border-color', 'red');
				$('#code_no_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#code_no_j').css('box-shadow', '');
				$('#code_no_j').css('border-color', '');
			}

			if (serial_no_j == "") {
				var msg = 'Enter serial number';
				$('#serial_no_j').focus();
				$('#serial_no_j').css('border-color', 'red');
				$('#serial_no_j').css('box-shadow', '0 0 0.15rem crimson');
				message_error(msg);
				return false;
			} else {
				$('#serial_no_j').css('box-shadow', '');
				$('#serial_no_j').css('border-color', '');
			}


			$("#loaderId").show();

			$.ajax({
				type: "POST",
				url: "coe_convocation_profile_correction_ajax.php",
				data: {
					pagetoken: pagetoken,
					category: category,
					institutionCode_j: institutionCode_j,
					row_id_j: row_id_j,
					univ_reg_no_j: univ_reg_no_j,
					collegetype_j: collegetype_j,
					degreeId_j: degreeId_j,
					courseId_j: courseId_j,
					conv_id_j: conv_id_j,
					student_name_en_j: student_name_en_j,
					student_name_ta_j: student_name_ta_j,
					convocation_year_j: convocation_year_j,
					convocation_month_j: convocation_month_j,
					total_marks_j: total_marks_j,
					code_no_j: code_no_j,
					serial_no_j: serial_no_j,
					grade_name_en_j: grade_name_en_j,
					title_of_the_thesis_j: title_of_the_thesis_j,
					type: type,
					purpose: purpose
				},
				cache: false,
				success: function(response) {
					$('#loaderId').delay(200).fadeOut('slow');

					<?php /* 
					alert(response);
					console.log(response);
					*/ ?>

					<?php /* alert(response); console.log(response); */ ?>

					if (response == 3) {
						$('#course').val("");
						var msg = 'Some data missing. Please logout and try again.!';
						message_error(msg);
						return false;
					} else if (response == 5) {
						$('#course').val("");
						var msg = 'Failed.!';
						message_error(msg);
						return false;
					} else if (response == 4) {
						var msg = 'No records found. Based on the search option.';
						message_error(msg);
						return false;
					} else if (response == 6) {
						var msg = 'Unable to modify student details. Please try again.';
						message_error(msg);
						return false;
					} else if (response.trim() == '1') {
						$.confirm({
							title: 'Alert!',
							content: 'Profile modification completed successfully.!',
							type: 'blue',
							typeAnimated: true,
							buttons: {
								tryAgain: {
									text: 'Ok',
									btnClass: 'btn-blue',
									action: function() {
										$('#popupBox').modal('hide');
									}
								}
							}
						});
					}


				}
			});
		}
	</script>
</body>

</html>