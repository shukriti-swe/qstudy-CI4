<?php

class SubjectClass
{
    public function __construct(){
        $this->db = db_connect();         
    }

    public function all($conditions = [])
    {
        return $this->db->table('tbl_subject')
            ->where($conditions)
            ->get()
            ->getResultArray();
    }

    public function chaptersOfSubject($subjectId)
    {   

        if (count($subjectId)) {
            if (count($subjectId)>1) {
                $res=$this->db->table('tbl_chapter')->whereIn('subjectId', $subjectId)->get()->getResultArray();
            } else {
                $res=$this->db->table('tbl_chapter')->where('subjectId', $subjectId[0])->get()->getResultArray();
            }
        }

        return $res;
    }

    public function search($tableName, $params)
    {
        $res = $this->db->table($tableName)
            ->where($params)
            ->get()
            ->getResultArray();

        return $res;
    }

    public function deleteChapter($chapterId)
    {
        //delete all question associated with this chapter
        $this->db->table('tbl_question')
            ->where('chapter', $chapterId)
            ->delete();
        //delete chapter
        $this->db->table('tbl_chapter')
            ->where('id', $chapterId)
            ->delete();
    }

    public function deleteSubject($subjectId)
    {
        //get all chapters associated
        $chapters = $this->chaptersOfSubject([$subjectId]);

        //delete all chapter associated
        foreach ($chapters as $chapter) {
            $this->deleteChapter($chapter['id']);
        }

        //delete subject
        $this->db->table('tbl_subject')
            ->where('subject_id', $subjectId)
            ->delete();
    }

    public function updateSubject($data,$subject_id)
    {
        $result=$this->db->table('tbl_subject')->where('subject_id', $subject_id)->update($data);
        
        if ($result)
        {
            return true;
        }else
        {
            return false;
        }
    }
}