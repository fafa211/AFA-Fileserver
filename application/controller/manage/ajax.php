<?php

class Ajax_Controller extends Controller{
    
    public function statistic_Action(){
        $tag = input::get('tag');

        $tagArr = explode(',', $tag);

        $dataArr = array();
        foreach($tagArr as $v){
            $dataArr[] = $this->statisticData($v);
        }

        $this->echojson(array('labels'=>explode(',','16-10,16-11,16-12,17-01,17-02,17-03,17-04'), 'data'=>$dataArr));
    }

    private function statisticData($tag){
        switch($tag){
            case 'new_user':
                return explode(',', '0,59,34,0,6,10,5');
            case 'user_valid':
                return explode(',', '0,65,39,10,6,3,9');
            case 'new_question':
                return explode(',', '0,22,39,10,6,3,9');
            case 'new_answer':
                return explode(',', '0,25,39,21,6,41,9');
            case 'new_topic':
                return explode(',', '0,25,39,45,6,3,9');
            case 'new_favorite_item':
                return explode(',', '0,56,39,10,6,3,9');
            case 'new_question_redirect':
                return explode(',', '0,25,45,23,6,3,9');
            case 'new_answer_vote':
                return explode(',', '0,14,22,10,61,3,9');
            case 'new_answer_thanks':
                return explode(',', '0,5,23,10,6,3,9');
            case 'new_question_thanks':
                return explode(',', '0,34,12,10,6,3,9');
        }
    }
    
}