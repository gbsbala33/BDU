<?php

include("includes/session_include.php");
include("includes/enc_conn_include.php");
include("includes/header_validate_code.php");


/*	DISPLAY STUDENT PROFILE REPORT IN INSTITUTION LOGIN AND COE LOGIN	*/
if (isset($_POST['type']) && validateInputData($_POST['type']) == 'getConvStudDtType') {
	if (isset($_POST['mode']) && validateInputData($_POST['mode']) == 'getConvStudDtMode') {
		if (validateInputData($_POST['pagetoken']) == validateInputData($_SESSION["pagetoken"])) {
			$category = validateInputData($_POST['category']);
			$convocationId_p = validateInputData($_POST['convocationId']);
			$certificateType_p = validateInputData($_POST['certificateType']);
			$collegetype_p = validateInputData($_POST['collegetype']);
			$rowId_p = validateInputData($_POST['row_id']);
			$institutionCode_p = validateInputData($_POST['institutionCode']);
			$degreeId_p = validateInputData($_POST['degreeId']);
			$courseId_p = validateInputData($_POST['courseId']);

			if ($_SESSION["category"] != killchars(trim($category))) { ?>
				<script>
					alert('<?php echo "Invalid Access"; ?>');
					window.location = "logout.php";
				</script>
			<?php exit;
			}


			if (empty($category) || empty($convocationId_p) || empty($certificateType_p) || empty($institutionCode_p) || empty($degreeId_p) || empty($courseId_p)) {
				echo 3;
				exit;
			}


			// (SELECT convocation.function_find_degree_short_code(degree_code::integer)) as degree_short_code,
			//(SELECT convocation.function_find_course_name_en(course_code::integer)) as course_name_en,

			// SELECT DEGREE NAME 
			$selDegreeName = "SELECT (SELECT convocation.function_find_degree_short_code(degree_id::integer)) as degree_short_code 
			FROM convocation.mst_convocation_degree
			WHERE degree_id=:degreeId_p";

			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeDegreeName = $db->prepare($selDegreeName);
				$exeDegreeName->bindParam(':degreeId_p', $degreeId_p, PDO::PARAM_STR);
				$exeDegreeName->execute();
				$resDegreeName = $exeDegreeName->fetch(PDO::FETCH_ASSOC);
				$degreeName = $resDegreeName['degree_short_code'];
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}


			// SELECT COURSE NAME 
			$selCourseName = "SELECT (SELECT convocation.function_find_course_name_en(course_id::integer)) as course_name
			FROM convocation.mst_convocation_course
			WHERE course_id=:courseId_p";

			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeCourseName = $db->prepare($selCourseName);
				$exeCourseName->bindParam(':courseId_p', $courseId_p, PDO::PARAM_STR);
				$exeCourseName->execute();
				$resCourseName = $exeCourseName->fetch(PDO::FETCH_ASSOC);
				$course_name = $resCourseName['course_name'];
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}


			// SELECT CERTIFICATE TYPE
			$selCertifiType = "SELECT (SELECT convocation.function_find_mst_certificate_type(certificate_id::integer)) as certificate_type
			FROM convocation.mst_certificate_type
			WHERE certificate_id=:certificateType_p";

			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeCertifiType = $db->prepare($selCertifiType);
				$exeCertifiType->bindParam(':certificateType_p', $certificateType_p, PDO::PARAM_STR);
				$exeCertifiType->execute();
				$resCertifiType = $exeCertifiType->fetch(PDO::FETCH_ASSOC);
				$certificateType = $resCertifiType['certificate_type'];
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}



			if ($collegetype_p == 'D') {
				$collegetype_p = 'UD';
			}

			// IN PHD DEGREE THERE IS NO AUTO/NON-AUTO TYPE. SO, WE ARE EXCLUDING THIS CONDITION.
			$collegeType = "";
			if ($certificateType_p != '3') {
				$collegeType = " AND auto_non_auto ='" . $collegetype_p . "'";
			}

			// SELECT row_id, academic_year, convocation_id, institution_code, univ_reg_no, student_name_en, student_name_ta, degree_code, course_code, convocation_year, convocation_month, total_marks, code_no, grade_name_en, grade_name_ta, certificate_type, convocation_type, university, unique_code, conv_certificate_issue_status, conv_certificate_issue_by, conv_certificate_issue_date, conv_certificate_issue_ip, add_by, add_date, add_ip, edit_by, edit_date, edit_ip, delete_by, delete_date, delete_ip, delete_flag, remarks_1, remarks_2, remarks_3 FROM convocation.trn_student_convocation_profile;



			/*
			$selStudProfile = "SELECT univ_reg_no, student_name_en, student_name_ta, grade_name_en, 
			(SELECT convocation.function_find_degree_short_code(degree_code::integer)) as degree_short_code,
			(SELECT convocation.function_find_course_name_en(course_code::integer)) as course_name_en,
			(SELECT convocation.function_find_mst_certificate_type(certificate_type::integer)) as certificate_type_name,
			conv_certificate_issue_status as issue_status FROM convocation.trn_student_convocation_profile
			WHERE convocation_id='" . $convocationId_p . "'
			AND institution_code='" . $institutionCode_p . "'
			AND degree_code='" . $degreeId_p . "'
			AND course_code='" . $courseId_p . "'
			AND certificate_type='" . $certificateType_p . "'
			AND auto_non_auto ='" . $collegetype_p . "'
			AND (student_name_ta!='' AND student_name_ta IS NOT NULL)
			AND delete_flag = '0'";

			echo "A===" . $selStudProfile;
			exit;
			*/

			// SELECT STUDENT PROFILE
			$selStudProfile = "SELECT univ_reg_no, student_name_en, student_name_ta, grade_name_en, student_photo,
			conv_certificate_issue_status as issue_status FROM convocation.trn_student_convocation_profile
			WHERE convocation_id=:convocationId_p
			AND institution_code=:institutionCode_p
			AND degree_code=:degreeId_p
			AND course_code=:courseId_p
			AND certificate_type=:certificateType_p
			$collegeType
			AND (student_name_ta!='' AND student_name_ta IS NOT NULL)
			AND delete_flag = '0'
			AND conv_certificate_issue_status = '1'
			ORDER BY univ_reg_no, student_name_en";

			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeStudProfile = $db->prepare($selStudProfile);
				$exeStudProfile->bindParam(':convocationId_p', $convocationId_p, PDO::PARAM_STR);
				$exeStudProfile->bindParam(':certificateType_p', $certificateType_p, PDO::PARAM_STR);
				//$exeStudProfile->bindParam(':collegetype_p', $collegetype_p, PDO::PARAM_STR);
				$exeStudProfile->bindParam(':institutionCode_p', $institutionCode_p, PDO::PARAM_STR);
				$exeStudProfile->bindParam(':degreeId_p', $degreeId_p, PDO::PARAM_STR);
				$exeStudProfile->bindParam(':courseId_p', $courseId_p, PDO::PARAM_STR);
				$exeStudProfile->execute();
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}

			if ($exeStudProfile->rowCount() == 0) {
				echo 4;
				exit;
			}

			echo '<div class="container">
				<table id="tableData" class="table table-striped table-bordered" style="width:100%;">';

			/*
			if ($exeStudProfile->rowCount() > 0) {
				echo '<div class="col-md-12">
					<div class="col-md-11"></div>
					<div class="col-md-1">
					
					<input class="btn btn-primary" type="button" onclick="getConvStudReport(\'' . $convocationId_p . '\',\'' . $certificateType_p . '\',\'' . $rowId_p . '\',\'' . $institutionCode_p . '\',' . $degreeId_p . ',' . $courseId_p . ')" name="generatepdf" value="Report" style="background: #006899;color: #fff;"/>
					</div>
					</div>';
			}
					*/

			echo '<label style="font-size:15px;color:#063;">
				Conv. ' . $convocationId_p . ' - ' . strtoupper($certificateType) . ' - ' . $institutionCode_p . ' - ' . $degreeName . ' - ' . $course_name . '</label>
					<thead>
						<tr>
							<th width="5%" style="padding: 10px;">#</th>
							<th width="15%" style="padding: 10px;">Univ&nbsp;Reg&nbsp;No</th>
							<th width="15%" style="padding: 10px;">Student&nbsp;Photo</th>
							<th width="15%" style="padding: 10px;">Student&nbsp;Name in En </th>
							<th width="15%" style="padding: 10px;">Student&nbsp;Name in TA</th>
							<th width="5%" style="padding: 10px;">Operations</th>
						</tr>
					</thead>
					<tbody>';

			if ($exeStudProfile->rowCount() > 0) {
				$i = 1;
				while ($row = $exeStudProfile->fetch(PDO::FETCH_ASSOC)) {
					$studPhoto = '';

					$univ_reg_no = $row['univ_reg_no'];
					$student_name_en = $row['student_name_en'];
					$student_name_ta = $row['student_name_ta'];
					$student_photo = $row['student_photo'];


					/*
							$selStudentPhoto = "SELECT tamil_name, student_photo FROM inst.trn_student_profile_ug 
							WHERE univ_reg_no=:resUnivRegNo";
							// Set the PDO error mode to exception
							$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							try {
								$exeStudentPhoto = $db->prepare($selStudentPhoto);
								$exeStudentPhoto->bindParam(':resUnivRegNo', $univ_reg_no, PDO::PARAM_STR);
								$exeStudentPhoto -> execute();
								$resStudentPhoto=$exeStudentPhoto->fetch(PDO::FETCH_ASSOC);
								$student_photo = $resStudentPhoto['student_photo'];
								$tamilName = $resStudentPhoto['tamil_name'];
							} catch (PDOException $e) {
								//Do your error handling here
								$message = $e->getMessage();
								echo "POD ERROR 1 <br>". $message . "<br><br>";
							}
							*/

					//$student_photo = "https://exams1.bdu.ac.in/bducoe/image/convocation_photo/001_stud_photo/".$univ_reg_no.".jpg";


					if ($student_photo != '')
						$studPhoto = '<img src=data:image/jpeg;base64,' . $student_photo . ' style="height:100px; width:100px;">';

					if ($student_name_ta == '')
						$student_name_ta = $tamilName;


					echo '<tr>
						  <td style="padding: 10px;">' . $i . '</td>
						  <td style="padding: 10px;"><a href="#" onclick="#" class="btn btn-success btn-sm">' . $univ_reg_no . '</a></td>
							<td style="padding: 10px;">' . $studPhoto . '</td>
						  <td style="padding: 10px;">' . strtoupper($student_name_en) . '</td>
						  <td style="padding: 10px;">' . $student_name_ta . '</td>
						  
						 <td>
								<button type="button" class="btn btn-primary" onclick="modifyStudentDetails(\'' . htmlentities($convocationId_p) . '\',\'' . htmlentities($institutionCode_p) . '\',\'' . htmlentities($rowId_p) . '\',\'' . htmlentities($univ_reg_no) . '\',\'' . htmlentities($collegetype_p) . '\',\'' . htmlentities($degreeId_p) . '\',\'' . htmlentities($courseId_p) . '\')">Edit</button>
							</td>
						</tr>';
					$i++;
				}

				echo ' <input type="hidden" name="total_count" id="total_count" value="' . $i . '">';
			} else {
				echo "<tr><td colspan='5' style='color:red; font-weight:bold;text-align:center;'>No Records Found..!!</td></tr>";
			}


			echo ' </tbody>
			</table>
			 </div>';
		} else {
			echo 'error';
			exit;
		}
	}
}







/*	DISPLAY STUDENT PROFILE REPORT IN INSTITUTION LOGIN AND COE LOGIN	*/
if (isset($_POST['type']) && validateInputData($_POST['type']) == 'modifyStudentMasterDetails') {
	if (isset($_POST['mode']) && validateInputData($_POST['mode']) == 'modifyStudentMasterDetailsMode') {
		if (validateInputData($_POST['pagetoken']) == validateInputData($_SESSION["pagetoken"])) {

			$category = validateInputData($_POST['category']);
			$convocationId_p = validateInputData($_POST['convocationId']);
			$institutionCode_p = validateInputData($_POST['institutionCode']);
			$rowId_p = validateInputData($_POST['row_id']);
			$univRegNo_p = validateInputData($_POST['univ_reg_no']);
			$collegetype_p = validateInputData($_POST['collegetype']);
			$degreeId_p = validateInputData($_POST['degreeId']);
			$courseId_p = validateInputData($_POST['courseId']);

			if ($_SESSION["category"] != killchars(trim($category))) { ?>
				<script>
					alert('<?php echo "Invalid Access"; ?>');
					window.location = "logout.php";
				</script>
			<?php exit;
			}


			if (empty($convocationId_p) || empty($institutionCode_p) || empty($rowId_p) || empty($univRegNo_p) || empty($collegetype_p) || empty($degreeId_p) || empty($courseId_p)) {
				echo 3;
				exit;
			}


			/*
			// RAW DATA 
			$selStudentProfile = "SELECT student_name_en,student_name_ta,convocation_year,convocation_month,total_marks,code_no,serial_no,grade_name_en,grade_name_ta,cla_pra_en, auto_non_auto,distance_ft_pt,title_of_the_thesis,certificate_type FROM convocation.trn_student_convocation_profile
			WHERE convocation_id='".$convocationId_p."' 
			AND institution_code='".$institutionCode_p."' 
			AND institution_row_id='".$rowId_p."'
			AND univ_reg_no='".$univRegNo_p."' 
			AND degree_code='".$degreeId_p."' 
			AND course_code='".$courseId_p."' 
			AND auto_non_auto='".$collegetype_p."' 
			AND conv_certificate_issue_status = '0'
			AND delete_flag='0'";	

			echo "A===".$selStudentProfile; exit;
			*/


			$selStudentProfile = "SELECT student_name_en,student_name_ta,convocation_year,convocation_month,total_marks,code_no,serial_no,grade_name_en,grade_name_ta,cla_pra_en, auto_non_auto,distance_ft_pt,title_of_the_thesis,certificate_type FROM convocation.trn_student_convocation_profile
			WHERE convocation_id=:convocationId_p 
			AND institution_code=:institutionCode_p 
			AND institution_row_id=:rowId_p
			AND univ_reg_no=:univRegNo_p 
			AND degree_code=:degreeId_p 
			AND course_code=:courseId_p 
			AND auto_non_auto=:collegetype_p 
			AND conv_certificate_issue_status = '1'
			AND delete_flag='0'";

			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeStudentProfile = $db->prepare($selStudentProfile);
				$exeStudentProfile->bindParam(':convocationId_p', $convocationId_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':institutionCode_p', $institutionCode_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':rowId_p', $rowId_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':univRegNo_p', $univRegNo_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':collegetype_p', $collegetype_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':degreeId_p', $degreeId_p, PDO::PARAM_STR);
				$exeStudentProfile->bindParam(':courseId_p', $courseId_p, PDO::PARAM_STR);
				$exeStudentProfile->execute();
				$resStudentProfile = $exeStudentProfile->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}

			if ($exeStudentProfile->rowCount() == 0) {
				echo json_encode(["status" => "error", "message" => "No record found"]);
				exit;
			}

			echo json_encode([
				"status" => "success",
				"student_name_en" => htmlspecialchars($resStudentProfile['student_name_en'], ENT_QUOTES),
				"student_name_ta" => htmlspecialchars($resStudentProfile['student_name_ta'], ENT_QUOTES),
				"convocation_year" => htmlspecialchars($resStudentProfile['convocation_year'], ENT_QUOTES),
				"convocation_month" => htmlspecialchars($resStudentProfile['convocation_month'], ENT_QUOTES),
				"total_marks" => htmlspecialchars($resStudentProfile['total_marks'], ENT_QUOTES),
				"code_no" => htmlspecialchars($resStudentProfile['code_no'], ENT_QUOTES),
				"serial_no" => htmlspecialchars($resStudentProfile['serial_no'], ENT_QUOTES),
				"grade_name_en" => htmlspecialchars($resStudentProfile['grade_name_en'], ENT_QUOTES),
				"grade_name_ta" => htmlspecialchars($resStudentProfile['grade_name_ta'], ENT_QUOTES),
				"cla_pra_en" => htmlspecialchars($resStudentProfile['cla_pra_en'], ENT_QUOTES),
				"auto_non_auto" => htmlspecialchars($resStudentProfile['auto_non_auto'], ENT_QUOTES),
				"distance_ft_pt" => htmlspecialchars($resStudentProfile['distance_ft_pt'], ENT_QUOTES),
				"title_of_the_thesis" => htmlspecialchars($resStudentProfile['title_of_the_thesis'], ENT_QUOTES),
				"certificate_type" => htmlspecialchars($resStudentProfile['certificate_type'], ENT_QUOTES)
			]);
		}
	}
}





/*	DISPLAY STUDENT PROFILE REPORT IN INSTITUTION LOGIN AND COE LOGIN	*/
if (isset($_POST['type']) && validateInputData($_POST['type']) == 'changeStudentConvDetails') {
	if (isset($_POST['purpose']) && validateInputData($_POST['purpose']) == 'modifyStudentConvDetails') {
		if (validateInputData($_POST['pagetoken']) == validateInputData($_SESSION["pagetoken"])) {

			$category = validateInputData($_POST['category']);
			$institutionCode_p = validateInputData($_POST['institutionCode_j']);
			$rowId_p = validateInputData($_POST['row_id_j']);
			$univRegNo_p = validateInputData($_POST['univ_reg_no_j']);
			$collegetype_p = validateInputData($_POST['collegetype_j']);
			$degreeId_p = validateInputData($_POST['degreeId_j']);
			$courseId_p = validateInputData($_POST['courseId_j']);
			$conv_id_p = validateInputData($_POST['conv_id_j']);
			$student_name_en_p = validateInputData($_POST['student_name_en_j']);
			$student_name_ta_p = validateInputData($_POST['student_name_ta_j']);
			$convocation_year_p = validateInputData($_POST['convocation_year_j']);
			$convocation_month_p = validateInputData($_POST['convocation_month_j']);
			$total_marks_p = validateInputData($_POST['total_marks_j']);
			$code_no_p = validateInputData($_POST['code_no_j']);
			$serial_no_p = validateInputData($_POST['serial_no_j']);
			$grade_name_en_p = validateInputData($_POST['grade_name_en_j']);
			$title_of_the_thesis_p = validateInputData($_POST['title_of_the_thesis_j']);


			if ($_SESSION["category"] != killchars(trim($category))) { ?>
				<script>
					alert('<?php echo "Invalid Access"; ?>');
					window.location = "logout.php";
				</script>
<?php exit;
			}


			if (empty($institutionCode_p) || empty($rowId_p) || empty($univRegNo_p) || empty($collegetype_p) || empty($degreeId_p) || empty($courseId_p) || empty($conv_id_p) || empty($student_name_en_p) || empty($convocation_year_p) || empty($convocation_month_p) || empty($total_marks_p) || empty($code_no_p) || empty($serial_no_p)) {
				echo 3;
				exit;
			}


			$db->query("BEGIN WORK");



			if ($grade_name_en_p != '' && $grade_name_en_p != NULL) {

				// SELECT THE TAMIL NAME BASED ON THE GRADE NAME PROVIDED IN ENGLISH
				$selGradeName = "SELECT grade_name_english, grade_name_tamil FROM convocation.mst_convocation_grade
							WHERE grade_name_english =:grade_name_en_p";
				// Set the PDO error mode to exception
				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				try {
					$exeGradeName = $db->prepare($selGradeName);
					$exeGradeName->bindParam(':grade_name_en_p', $grade_name_en_p, PDO::PARAM_STR);
					$exeGradeName->execute();
					$resGradeName = $exeGradeName->fetch(PDO::FETCH_ASSOC);
					$gradeNameEnglish = $resGradeName['grade_name_english'];
					$gradeNameTamil = $resGradeName['grade_name_tamil'];

					if ($gradeNameTamil != "") {
						$gradeName =  " grade_name_en='" . $gradeNameEnglish . "', grade_name_ta='" . $gradeNameTamil . "', ";
					}
				} catch (PDOException $e) {
					//Do your error handling here
					$message = $e->getMessage();
					echo "POD ERROR 1 <br>" . $message . "<br><br>";
				}
			}



			/* $insQueryBackup = "INSERT INTO convocation.trn_student_convocation_profile_history(
			academic_year, convocation_id, institution_code, univ_reg_no, student_name_en, student_name_ta, degree_code, course_code, convocation_year, convocation_month, total_marks, code_no, serial_no, grade_name_en, grade_name_ta, cla_pra_en, issue_date_temp, auto_non_auto, distance_ft_pt, student_photo, title_of_the_thesis, certificate_type, university, unique_code, conv_certificate_issue_status, conv_certificate_issue_by, conv_certificate_issue_date, conv_certificate_issue_ip, add_by, add_date, add_ip, edit_by, edit_date, edit_ip, delete_by, delete_date, delete_ip, delete_flag, remarks_1, remarks_2, remarks_3)
			SELECT academic_year, convocation_id, institution_code, univ_reg_no, student_name_en, student_name_ta, degree_code, course_code, convocation_year, convocation_month, total_marks, code_no, serial_no, grade_name_en, grade_name_ta, cla_pra_en, issue_date_temp, auto_non_auto, distance_ft_pt, student_photo, title_of_the_thesis, certificate_type, university, unique_code, conv_certificate_issue_status, conv_certificate_issue_by, conv_certificate_issue_date, conv_certificate_issue_ip, add_by, add_date, add_ip, edit_by, edit_date, edit_ip, delete_by, delete_date, delete_ip, delete_flag, remarks_1, remarks_2, remarks_3
			FROM convocation.trn_student_convocation_profile
			WHERE institution_code='" . $institutionCode_p . "' 
			AND institution_row_id='" . $rowId_p . "' 
			AND univ_reg_no='" . $univRegNo_p . "'  
			AND degree_code='" . $degreeId_p . "'  
			AND course_code='" . $courseId_p . "'  
			AND auto_non_auto='" . $collegetype_p . "'  
			AND conv_certificate_issue_status = '1'
			AND delete_flag='0'";
			echo "K===>>>" . $insQueryBackup;
			exit; */

			$insQueryBackup = "INSERT INTO convocation.trn_student_convocation_profile_history(
			academic_year, convocation_id, institution_code, univ_reg_no, student_name_en, student_name_ta, degree_code, course_code, convocation_year, convocation_month, total_marks, code_no, serial_no, grade_name_en, grade_name_ta, cla_pra_en, issue_date_temp, auto_non_auto, distance_ft_pt, title_of_the_thesis, certificate_type, university, unique_code, conv_certificate_issue_status, conv_certificate_issue_by, conv_certificate_issue_date, conv_certificate_issue_ip, edit_by, edit_date, edit_ip,  delete_flag, remarks_1, remarks_2, remarks_3)

			SELECT academic_year, convocation_id, institution_code, univ_reg_no, student_name_en, student_name_ta, degree_code, course_code, convocation_year, convocation_month, total_marks, code_no, serial_no, grade_name_en, grade_name_ta, cla_pra_en, issue_date_temp, auto_non_auto, distance_ft_pt, title_of_the_thesis, certificate_type, university, unique_code, conv_certificate_issue_status, conv_certificate_issue_by, conv_certificate_issue_date, conv_certificate_issue_ip, :user_id, now(), :ip_address, delete_flag, remarks_1, remarks_2, remarks_3
			FROM convocation.trn_student_convocation_profile
			WHERE institution_code=:institutionCode_p 
			AND institution_row_id=:rowId_p
			AND univ_reg_no=:univRegNo_p 
			AND degree_code=:degreeId_p 
			AND course_code=:courseId_p 
			AND auto_non_auto=:collegetype_p 
			AND conv_certificate_issue_status = '1'
			AND delete_flag='0'";


			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeInsQueryBackup = $db->prepare($insQueryBackup);
				$exeInsQueryBackup->bindParam(':institutionCode_p', $institutionCode_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':rowId_p', $rowId_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':univRegNo_p', $univRegNo_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':collegetype_p', $collegetype_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':degreeId_p', $degreeId_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':courseId_p', $courseId_p, PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':user_id', validateInputData($_SESSION['user_id']), PDO::PARAM_STR);
				$exeInsQueryBackup->bindParam(':ip_address', validateInputData($_SESSION['ip_address']), PDO::PARAM_STR);
				$exeInsQueryBackup->execute();
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}

			if (!$exeInsQueryBackup) {
				$db->query("ROLLBACK");
				echo '5';
				exit;
			}





			/* $upsQuery = "UPDATE convocation.trn_student_convocation_profile
			SET  convocation_id='" . $conv_id_p . "', student_name_en='" . $student_name_en_p . "', student_name_ta='" . $student_name_ta_p . "', convocation_year='" . $convocation_year_p . "', convocation_month='" . $convocation_month_p . "', total_marks='" . $total_marks_p . "', code_no='" . $code_no_p . "', serial_no='" . $serial_no_p . "', grade_name_en='" . $gradeNameEnglish . "', grade_name_ta='" . $gradeNameTamil . "',  title_of_the_thesis='" . $title_of_the_thesis_p . "',  edit_by='" . validateInputData($_SESSION['user_id']) . "', edit_date=now(), edit_ip='" . validateInputData($_SESSION['user_id']) . "'
			WHERE institution_code='" . $institutionCode_p . "' 
			AND institution_row_id='" . $rowId_p . "' 
			AND univ_reg_no='" . $univRegNo_p . "'  
			AND degree_code='" . $degreeId_p . "'  
			AND course_code='" . $courseId_p . "'  
			AND auto_non_auto='" . $collegetype_p . "'  
			AND conv_certificate_issue_status = '1'
			AND delete_flag='0'";
			echo "K==>>>" . $upsQuery;
			exit; */




			$upsQuery = "UPDATE convocation.trn_student_convocation_profile
			SET  convocation_id=:conv_id_p, student_name_en=:student_name_en_p, student_name_ta=:student_name_ta_p, convocation_year=:convocation_year_p, convocation_month=:convocation_month_p, total_marks=:total_marks_p, code_no=:code_no_p, serial_no=:serial_no_p, title_of_the_thesis=:title_of_the_thesis_p, 
			$gradeName
			edit_by=:user_id, edit_date=now(), edit_ip=:ip_address
			WHERE institution_code=:institutionCode_p 
			AND institution_row_id=:rowId_p
			AND univ_reg_no=:univRegNo_p 
			AND degree_code=:degreeId_p 
			AND course_code=:courseId_p 
			AND auto_non_auto=:collegetype_p 
			AND conv_certificate_issue_status = '1'
			AND delete_flag='0'";

			// 18
			// Set the PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			try {
				$exeUpsQuery = $db->prepare($upsQuery);
				$exeUpsQuery->bindParam(':institutionCode_p', $institutionCode_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':rowId_p', $rowId_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':univRegNo_p', $univRegNo_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':collegetype_p', $collegetype_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':degreeId_p', $degreeId_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':courseId_p', $courseId_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':conv_id_p', $conv_id_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':student_name_en_p', $student_name_en_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':student_name_ta_p', $student_name_ta_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':convocation_year_p', $convocation_year_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':convocation_month_p', $convocation_month_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':total_marks_p', $total_marks_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':code_no_p', $code_no_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':serial_no_p', $serial_no_p, PDO::PARAM_STR);
				//$exeUpsQuery->bindParam(':gradeNameEnglish', $gradeNameEnglish, PDO::PARAM_STR);
				//$exeUpsQuery->bindParam(':gradeNameTamil', $gradeNameTamil, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':title_of_the_thesis_p', $title_of_the_thesis_p, PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':user_id', validateInputData($_SESSION['user_id']), PDO::PARAM_STR);
				$exeUpsQuery->bindParam(':ip_address', validateInputData($_SESSION['ip_address']), PDO::PARAM_STR);
				$exeUpsQuery->execute();
			} catch (PDOException $e) {
				//Do your error handling here
				$message = $e->getMessage();
				echo "POD ERROR 1 <br>" . $message . "<br><br>";
			}

			if (!$exeUpsQuery) {
				$db->query("ROLLBACK");
				echo '6';
				exit;
			} else {
				$db->query("COMMIT;");
				echo '1';
				exit;
			}
		}
	}
}




?>