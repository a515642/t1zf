<?php
	include("../function.php");
	if(!$admin)code(["code"=>"-1","message"=>"Cookie已到期请重新登陆！"]);
	$_POST = json_decode(file_get_contents('php://input'),true);
	
	
	$listtype=$_POST['listtype'];
	
	
	if($_POST['type']=='del'){
		$id=(int)$_POST['id'];
		if($listtype=='jf' || $listtype=='money'||$listtype=='vip'||$listtype=='custom'){}else code(["code"=>"0","message"=>"删除失败！"]);
		$sqlk = 'users_log_'.$listtype;
		$res = $sql("DELETE FROM ".$sqlk." WHERE id='{$id}' AND admin_id='".$admin['id']."' ");
		if($res)code(["code"=>"1","message"=>"删除成功！"]);
		else code(["code"=>"0","message"=>"删除失败！"]);
	}
	
	
	if($listtype=='jf' || $listtype=='money'||$listtype=='vip'||$listtype=='custom'){}else $listtype='money';
	$user=$_POST['user'];
	$search=$_POST["search"];
	$keyword=$_POST["keyword"];
	$nowpage=$_POST["nowpage"];
	$pagenum=$_POST["pagenum"];
	$orderby=$_POST["orderby"];
	
	if($search=="备注" && $keyword!=""){
		$where = " WHERE msg like '%".$keyword."%' AND user='".$user."' AND admin_id='".$admin['id']."'";
	}else if($search=="数值" && $keyword!=""){
		if($listtype=='custom'){
			$where = " WHERE custom like '%".$keyword."%' AND user='".$user."' AND admin_id='".$admin['id']."'";
		}else{
			if(is_set($keyword,'+'))str_replace("+",'',$keyword);
			$where = " WHERE num='".$keyword."' AND user='".$user."' AND admin_id='".$admin['id']."'";
		}
	}else if($search=="时间" && $keyword!=""){
		if(is_set($keyword,"大于")){
			$keyword=sj($keyword."狗东西66","大于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime > '".$keyword."' "." AND user='".$user."' AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"小于")){
			$keyword=sj($keyword."狗东西66","小于","狗东西66");
			if(strtotime($keyword)>999){
				$where = " WHERE addtime < '".$keyword."' "." AND user='".$user."' AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else if(is_set($keyword,"到")){
			$k1=sj("狗东西66".$keyword,"狗东西66",'到');
			$k2=sj($keyword."狗东西66","到","狗东西66");
			if(strtotime($k1)>999 && strtotime($k2)>999){
				$where = " WHERE addtime between '".$k1."'  AND '".$k2."' "." AND user='".$user."' AND admin_id='".$admin['id']."'";
			}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
		}else code(["code"=>"0","message"=>"时间格式输入错误！"]);
	}else{
		$where = " WHERE  admin_id='".$admin['id']."' AND user='".$user."'";
	}
	
	
	if($orderby=="id降序"){
		$orderby=" ORDER BY id desc ";
	}else if($orderby=="id升序"){
		$orderby=" ORDER BY id asc ";
	}else{
		$orderby=" ORDER BY id desc ";
	}
	
	
	$sqlk = 'users_log_'.$listtype;
	
	
	$sqlnum = $sql("SELECT count(*) from ".$sqlk." ".$where.$orderby);
	if($pagenum=="10" || $pagenum=="30" || $pagenum=="50" || $pagenum=="100"){}else $pagenum=30;
	$maxpage=intval($sqlnum/$pagenum);
	if ($sqlnum%$pagenum)$maxpage++;
	$nowpage=(int)$nowpage;
	if($nowpage=="" || $nowpage<1 || $nowpage>$maxpage)$nowpage=1;
	$offset=$pagenum*($nowpage-1);
	$limit = " LIMIT {$offset},{$pagenum} ";
	
	
	$res = $sql("SELECT * FROM ".$sqlk." ".$where.$orderby.$limit,"list");
	
	$json = array();
	$i=0;
	foreach($res as $val){
		
		if($listtype=='custom')$val['num']=$val['custom'];
		if($val['num']>0)$val['num'] = '+'.$val['num'];
		$json[$i++] = [
			'id'=>$val['id'],
			'num'=>$val['num'],
			'message'=>$val['msg'],
			'addtime'=>$val['addtime']
		];
		
	}
	
	code(["code"=>"1","message"=>"获取成功！","data"=>$json,"modle"=>[
		
		'search'=>$_POST["search"],
		'keyword'=>$_POST["keyword"],
		'nowpage'=>$_POST["nowpage"],
		'pagenum'=>$_POST["pagenum"],
		'orderby'=>$_POST["orderby"],
		'maxpage'=>$maxpage,
		'havenum'=>$sqlnum,
		'listtype'=>$listtype
		
	]]);
	
	
	
	
	
	
