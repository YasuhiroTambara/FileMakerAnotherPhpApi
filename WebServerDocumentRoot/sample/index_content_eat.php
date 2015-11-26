<?php
//共通の環境設定
require_once './ini.php';
//FileMakerサーバーとの通信を利用する準備
require_once './connect.php';

$dates = $_GET['date'];
if ( !is_array($dates) ) exit();

$dates[0] = strftime('%Y/%m/%d', strtotime($dates[0]));
$dates[1] = strftime('%Y/%m/%d', strtotime($dates[1]));
$dates = implode ('...', $dates);

//$param : 検索スクリプトに渡す引数を生成
$param =
  //&occ : テーブルオカレンスの指定
  '&occ=Eat'.
  //&noSort : 
  //&noSort=True'.
  //&get : 値を取得するフィールド名を改行リストで指定
  '&get='. implode('\n', ['kp','dDate','iTime','tName']).
  //&crit : 検索条件を改行リストで指定
  '&crit='. implode('\n', [ 'Eat::dDate|' . $dates ]);
//検索スクリプト実行
$return =$API->Perform('Getter',$param);
$ret =$return['gtRet'];
$cov =$return['gnCov'];
?>
<div id="content"></div>

<script>
(function(global){
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
var i, conDate = null, html = '', qRow;
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 
/*
var us = 24*60*60*1000,//ミリ秒変換単位
    arr = $('#dDisplay0').val().split('-'),
    yy = arr[0],
    mm = arr[1],
    dd = arr[2],
    date0 = new Date(yy,wrapInt(mm)-1,dd),
    
    arr = $('#dDisplay1').val().split('-'),
    yy = arr[0],
    mm = arr[1],
    dd = arr[2],
    date1 = new Date(yy,wrapInt(mm)-1,dd);
*/
var date0 = new Date( $('#dDisplay0').val() ),
    date1 = new Date( $('#dDisplay1').val() );
// getTimeメソッドで経過ミリ秒を取得し、２つの日付の差を求める
var msDiff = date1.getTime() - date0.getTime();
//isNaN(msDiff)
// 求めた差分（ミリ秒）を日付へ変換（端数切り捨て）し、1日分加算して返却する
var daysDiff = Math.floor( msDiff / (1000 * 60 * 60 *24) ) +1;

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 指定範囲の各日付のリストをラップするブロック要素を用意する
for(i =0; i < daysDiff; i++){
  //alert( dateMove(date0,i) );
  conDate = textDate( new Date( dateMove(date0,i) ),'/');
  //
  html += 
    '<div class="entry">' +
      '<h3 class="section_title">' + //
        '<a style="float:left;cursor:pointer;"><i class="fa fa-minus-square fa-fw" title="たたむ"></i></a>' + // 開閉ボタン
        '<span class="date" style="float:left;">' + conDate + '</span>' + // YYYY/MM/DD の表示
        '<a href="#popupDay" class="popup_btn ex" style="float:left;margin-left:1em;cursor:pointer;">' +
          '<i class="fa fa-external-link-square"></i> MEMO</a>' + // 日付詳細(この日のメモ)ポップアップボタン
        
        '<a class="add" style="float:right;cursor:pointer;"><i class="fa fa-plus-circle"></i></a>' + // 追加ボタン
        '<span class="count" style="float:right;margin-right:.3em;">0行</span>' + // 行数の表示
      '</h3>' +
      '<div class="section_body"></div>' +
    '</div>';
  
}
$('div#content').append(html);



// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 
//var j = JSON.parse();
var j = <?=$ret ?>;
//alert (j);

// 
conDate = null;
html = '';
for(i =0; i <j.length; i++){
  
  testDate = decodeURIComponent(j[i].dDate);
  // 結果セットループの日付の変更行
  if( conDate !== testDate){
    // ループ初回以外
    if(conDate !== null){
      // 日付ごとに継ぎ足したhtmlをプリントしリセット
      qRow.children('.section_body').append(html);
      html = '';
      //行数を表示
      qRow.find('.section_title').find('.count').text( qRow.find('div.eat_row').length + '行' );
    }
    
    //走査日を上書き
    conDate = testDate;
    //qRow: 生成済みの走査日にごとにラップするブロックのjQueryオブジェクトの指定を更新
    qRow = $('#content .entry').filter( function(){
      return $(this).find('.date').text() === conDate;
    });
  }
  
  html += 
    '<div class="eat_row" alt="'+ j[i].kp +'">'+ 
      '<span class="eatImage">'+ '' +'</span>'+ //////
      //時刻を表示
      '<p class="time" style="float:left;">'+ timeSlice( decodeURIComponent(j[i].iTime) ) +'</p>'+
      //削除ボタン
      '<a class="del" style="float:right;font-size:1.4em;color:#E77;"><i class="fa fa-minus-circle"></i></a>' +
      //撮影ボタン
      //'<a class="pic" style="float:right;font-size:1.4em;margin-right:0.4em;"><i class="fa fa-camera"></i></i></a>' +
      //項目名を表示
      '<p class="eatName" style="clear:both;">'+ decodeURIComponent(j[i].tName) +'</p>'+
    '</div>';
}
if(qRow){
  //日付ごとに継ぎ足したhtmlをプリントする(ループ最後の最新の日付分)
  qRow.children('.section_body').append(html);
  //行数を表示
  qRow.find('.section_title').find('.count').text( qRow.find('div.eat_row').length + '行' );
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 
if(getDevice()==='tab' || getDevice()==='sp'){
  $('#dDisplay').css('width','138');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

})(this);
</script>