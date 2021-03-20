<?php
class PouetBoxIndexFeedPoundsOff extends PouetBoxCachable {
  function __construct() 
  {
    parent::__construct();
    $this->uniqueID = "pouetbox_feedpoundsoff";
    $this->title = "pounds-off !";

    $this->cacheTime = 60*60;

    $this->limit = 5;
  }

  use PouetFrontPage;
  function SetParameters($data)
  {
    if (isset($data["limit"])) $this->limit = $data["limit"];
  }
  function GetParameterSettings()
  {
    return array(
      "limit" => array("name"=>"number of posts visible","default"=>5,"min"=>1,"max"=>10),
    );
  }

  function LoadFromCachedData($data) {
    $this->jsonData = unserialize($data);
  }

  function GetCacheableData() {
    return serialize($this->jsonData);
  }

  function LoadFromDB() 
  {
    $this->jsonData = array();

    $sideload = new Sideload();
    $response = $sideload->Request('http://pounds-off.me/',"GET",array("format"=>"json"));
    if ($response)
    {  
      $this->jsonData = json_decode( $response, true );
    }
  }

  function RenderBody() {
    if (!$this->jsonData)
    {
      return;
    }
    echo "<ul class='boxlist'>\n";
    for($i=0; $i < min( count($this->jsonData),$this->limit); $i++)
    {
      echo "<li>\n";
      $p = "sucks";
      if($this->jsonData[$i]['status'] == "lost"   && $this->jsonData[$i]['intent'] == "lose weight") $p = "rulez";
      if($this->jsonData[$i]['status'] == "gained" && $this->jsonData[$i]['intent'] == "gain weight") $p = "rulez";
      if($this->jsonData[$i]['status'] == "hold"   && $this->jsonData[$i]['intent'] == "hold weight") $p = "rulez";
      if($this->jsonData[$i]['status'] == "hold"   && $this->jsonData[$i]['intent'] != "hold weight") $p = "isok";
      echo "<img src='".POUET_CONTENT_URL."gfx/".$p.".gif' alt='".$p."' />\n";
      echo "<a href='"._html($this->jsonData[$i]['url'])."'>"._html($this->jsonData[$i]['name'])."</a> "._html(strip_tags($this->jsonData[$i]['message']));
      echo "</li>\n";
    }
    echo "</ul>\n";
  }
  function RenderFooter() {
    echo "  <div class='foot'><a href='https://pounds-off.me/'>more at pounds-off</a>...</div>\n";
    echo "</div>\n";
  }
};

$indexAvailableBoxes[] = "FeedPoundsOff";
?>
