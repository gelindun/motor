<?php
class Search {
    public function __construct(){
        Vendor('CoreSeek.sphinxapi');
        $this->spClient = new SphinxClient ();
        $this->spClient->SetMatchMode(SPH_MATCH_AND);
        $this->spClient->SetServer ('10.232.82.126', 3313);
        $this->spClient->SetConnectTimeout (5);
        $this->spClient->SetArrayResult ( true );
        $this->spClient->SetSortMode(SPH_SORT_EXTENDED, '@relevance DESC, @id DESC');
        $this->_newsIndex = 'weixin_help';
    }
    
    function query($q, $limit=0, $start=0) {
        $_maxLimit = ($limit*$start)+$limit;
        $this->spClient->SetLimits($start, $limit, $_maxLimit);
        $res = $this->spClient->Query ($q, $this->_newsIndex);
        //dump($this->spClient);
        $this->spClient->ResetFilters();
        return $res;
    }
    
    public function setIndex($index) {
        $this->_newsIndex = $index;
    }
    
    public function setFilter($attribute, $values, $exclude=false) {
        $this->spClient->SetFilter($attribute, $values, $exclude);
    }
    
    public function setSortMode($fields) {
        if($fields) {
            $this->spClient->SetSortMode(SPH_SORT_EXTENDED, $fields);
        }
    }
    
    public function buildExcerpts($docs, $words) {
        $opts = array(
            "before_match"          => "<strong class='f_red'>",
            "after_match"           => "</strong>",
            "chunk_separator"       => "..",   
            "limit"                 => 100,   
            "around"                => 3 
        );
        $_r = $this->spClient->BuildExcerpts($docs, $this->_newsIndex, $words, $opts);
        return $_r[0];
    }
}