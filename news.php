<?php

require("Entities/functions.php");
require("Entities/NewsInfo.php");

$newsid=getParameter("id");
if(!$newsid) die("错误的参数");
$news=new NewsInfo();

$getNews=$redis->StringOperator->getFromReids("news".$newsid);  //从redis取

if(!$getNews) //如果木有取到
{
    $getNews=getFromDB($newsid);//就从数据库取
    if(!$getNews) //数据库里也没取到
        $getNews=defaultCache; //设置为默认缓存
    else
        $getNews=json_encode($getNews);//DB取到了 就把它变成JSON格式字符串

    $redis->StringOperator->setToRedis("news".$newsid,$getNews,200);//塞入缓存 ,过期时间为200秒。 测试时间，莫纠结
}
else
{
    echo "from cache";
}
if(isDefaultCache($getNews))
{
    $redis->StringOperator->expireCache("news".$newsid,5);// 给原来的缓存 增加5秒时间
    exit("别黑我了!!!");
}

echo $getNews;