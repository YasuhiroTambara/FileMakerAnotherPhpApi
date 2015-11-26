<?php
//共通の環境設定
require_once './ini.php';
header('Content-type: text/html;charset=UTF-8');
?><!DOCTYPE html>
<html lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no">
  <title>食日記</title>
  <link rel="shortcut icon" href="images/favicon.ico">
  <link rel="stylesheet" href="styles/style.css">
  <!--<link rel="stylesheet" href="styles/fontawsome/css/font-awesome.min.css">-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>

<div id="topbar">
  
  <div id="sidebar">
    <div id="sidebar-top">
      <a id="title">
        <span>My 食日記<br>
          <span id="version">1.0</span>
        </span>
      </a>
    </div>
  </div>
  
  <div class="topbar_content">
    <h1 style="overflow:auto;margin: 0 auto;width:auto;">
      <span>期間:</span>
      <input id="dDisplay0" type="date">
      <span>～</span>
      <input id="dDisplay1" type="date">
      <!--
      <button id="today" type="button" class="btn btn_default">
        今週
      </button>
      -->
    </h1>
  </div>
</div>

<div id="main">
  <div id="content"></div>
</div>

<div id="popupUtility" class="popup">
  <div class="popup_inner">
    <h3>
      <p style="float:left;"></p>
      <a href="#close_btn" class="btn_popup_close" style="float:right;"><i class="fa fa-times"></i></a>
    </h3>
    <form></form>
  </div>
</div>

<div id="popupDay" class="popup" style="min-width:24em;min-height:9em;">
  <div class="popup_inner">
    <summary style="clear: both;">
      <p class="title" style="float:left;">title</p>
      <a href="#close_btn" class="btn_popup_close" style="float:right;"><i class="fa fa-times"></i></a>
    </summary>
    
    <div style="padding-right: 1em;">
      <textarea class="memo" placeholder="memo" style="min-width:22em;min-height:7em;"></textarea>
    </div>
    
  </div>
</div>
<!--
<a href="#popupDay" class="popup_btn">
  <span>test</span>
</a>
-->

<div id="popupImage" class="popup">
  <div class="popup_inner">
    <h3>
      <p style="float:left;">イメージ</p>
      <a href="#close_btn" class="btn_popup_close" style="float:right;">//////</a>
    </h3>
    <form  id="" method="post" action="">
      <div id="">
        <input type="text" name="email_from" value="" placeholder="FROM">
        <textarea name="email_to" placeholder=""></textarea>
      </div>
      
      <p class="submit"><input type="submit" name="" value="Submit"></p>
    </form>
  </div>
</div>

<div id="overlay"></div>

<!--<scr ipt src="./scripts/jquery-2.1.3.min.js"></scr ipt>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ↓↓↓global scope
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
var preload ='<span id="preload"><i class="fa fa-fw fa-spinner fa-spin"></i>Loading...</span>';

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// Date型を「yyyy/mm/dd」に変換
var textDate = function(date,sep){
  var y = date.getFullYear(),
      m = ('0' + (date.getMonth() +1)).slice(-2),
      d = ('0' + date.getDate()).slice(-2);
     //,ww= d.getDay()//曜日
     //,cshift =(cww===0 ? -6 : 1-cww)//月曜始まり化
  return y+ sep +m+ sep +d;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
var popup = function(target){
  //
  var qPopup = $(target);
  
  //ポップアップの幅と高さからmarginを計算する
  var mT = (qPopup.outerHeight() / 2) * (-1) + 'px';
  var mL = (qPopup.outerWidth() / 2) * (-1) + 'px';

  // marginを設定して表示
  $('.popup').hide();
  qPopup.css({
      'margin-top': mT,
      'margin-left': mL
  }).show();
  //
  $('#overlay').show();
  return false;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//
$(document)
//ポップアップボタンの共通動作
.on('click', '.popup_btn', function(){
    //
    var qPopup = $($(this).attr('href'));
    
    //ポップアップの幅と高さからmarginを計算する
    var mT = (qPopup.outerHeight() / 2) * (-1) + 'px';
    var mL = (qPopup.outerWidth() / 2) * (-1) + 'px';

    // marginを設定して表示
    $('.popup').hide();
    qPopup.css({
        'margin-top': mT,
        'margin-left': mL
    }).show();
    //
    $('#overlay').show();
    return false;
})

//
.on('click', '.btn_popup_close, #overlay', function(){
    $('.popup, #overlay').hide();
    return false;
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//
var fail = function(){//console.log(dat);
  $('#popupUtility').find('p').html('サーバー接続に失敗しました。<br>電波状態を確認してください。');
  //$('#popupUtility').dialog('open');
  popup('#popupUtility');
  $('#content').html(preload);
  $(document).off('click','#overlay');
}
//fail();

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//日付範囲フィルターの初期値をセット
//$(document).data('dDisplay',new Date());

var us = 24*60*60*1000,//ミリ秒変換単位
    date = new Date(),
    yy = date.getFullYear(),
    mm = ('0' + (date.getMonth() +1)).slice(-2),
    dd = ('0' + date.getDate()).slice(-2),
    
    ms = date.getTime(),//日付をミリ秒単位に変換
    ww= date.getDay(),//曜日
    shift =(ww===0 ? -6 : 1-ww),//月曜始まり化
    date0 = new Date( ms + ( 0 + shift ) *us );

//alert(yy+'-'+mm+'-'+dd);
$('#dDisplay1').val( yy +'-'+ mm +'-'+ dd );
$('#dDisplay0').val( textDate( date0, '-' ) );

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ajax
var contentLoad = function(){
  $('#content').html(preload);
  $.ajax({
    type:'get',
    data:{'date':[$('#dDisplay0').val() ,$('#dDisplay1').val()]},
    url:'./index_content_eat.php'
  })
  .done(function(dat){//alert(dat);
    $('#main').html(dat);
  })
  .fail(function(){
    fail();
  });
}
contentLoad();//即時実行

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$('#dDisplay0,#dDisplay1').on('change', function(){
  //alert( $(this).val() );
  contentLoad();
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 入力待機化(食事名)
$(document).on('dblclick', '#content .eat_row p.eatName',
function(){
  // 入力欄がすでに起動している場合は終了
  if($(this).children('input:text')[0]) return false;//'input[type=text]'
  
  // 既存値をjQueryデータオブジェクトに確保
  var v =  $(this).text();
  $.data($(this)[0],'valPrev',v);
  // 既存値の表示を消す
  $(this).text('');
  //入力欄を生成し、既存値をセット
  $(this).append('<input type="text" value="'+ v +'" />');
  // 入力欄にフォーカス
  $(this).children('input:eq(0)').focus();
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 入力待機化(時刻)
$(document).on('dblclick', '#content .eat_row p.time',
function(){
  // 入力欄を既に表示している場合は終了
  if( $(this).children('input[type=time]').length >0 ) return false;
  
  // 既存値をjQueryデータオブジェクトに確保
  var v =  $(this).text();
  $.data($(this)[0],'valPrev',v);
  
  // 既存値の表示を消す
  $(this).text('');
  //入力欄を生成し、既存値をセット
  $(this).append('<input type="time" value="'+ v +'" />');
  // 入力欄にフォーカス
  $(this).children('input:eq(0)').focus();
});



// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 入力値(食事名)返却
$(document).on('blur', '#content .eat_row p.eatName input:text',
function(){
  // v :入力値、 keyvEat: 行の主キー値
  var v =  $(this).val(),
      keyvEat = $(this).closest('.eat_row').attr('alt');
  
  if($.data($(this).parent('p')[0],'valPrev') !== v){
    // 変更があったなら、AJAXによるアップデート実行
    $.ajax({
      type:'get',
      data:{'keyvEat':keyvEat,'value':v, 'col':'tName'},
      url:'./index_content_eat_setter.php'
    })
    .done(function(dat){
      if( dat !=='1') alert(dat +'正常にデータセットできませんでした。');
    })
    .fail(function(){
      fail();
      return;
    });
  }
  //入力値を表示テキストに移し変える
  $(this).parent('p').text(v);
  //入力欄を削除
  $(this).remove();
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 入力値(時刻)返却
$(document).on('blur', '#content .eat_row p.time input',
function(){
  // v :入力値、 keyv: 行の主キー値
  var qDateWrap = $(this).closest('.entry'),// 日付ラッパーオブジェクト
      v =  $(this).val(),
      keyv = $(this).closest('.eat_row').attr('alt');
  
  // AJAXによる更新が不要な場合
  if(v === ''){
    // 入力した日付の形式が完全ではない場合は、AJAXによるアップデートをせず、元の値の表示を復帰させる
    v = $.data($(this).parent('p')[0],'valPrev');
    
  // AJAXによる更新が必要な場合
  }else
  if($.data($(this).parent('p')[0],'valPrev') !== v){
    // 変更があったなら、AJAXによるアップデート実行
    $.ajax({
      type:'get',
      data:{'keyvEat':keyv,'value':v, 'col':'iTime'},
      url:'./index_content_eat_setter.php'
    })
    .done(function(dat){
      if( dat !=='1'){
        alert('正常にデータセットできませんでした。');
        // エラーだった場合は、元の値の表示を復帰させる
        v = $.data($(this).parent('p')[0],'valPrev');
      }else{
        // ソート位置まで移動
        qDateWrap.children('.section_body').html(
          qDateWrap.children('.section_body').children('.eat_row').sort(function(a, b) {
            return numTimeAmount( $(a).children('.time').text() ) - numTimeAmount( $(b).children('.time').text() );
          })
        );
        
      }
      
    })
    .fail(function(){
      fail();
    });
  
  }
  // 共通のファイナライズ
  $(this).parent('p').text(v);
  $(this).remove();
});



// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//追加ボタン
$(document)
.on('click', '#content .entry a.add',
function(){
  // 
  var qDateWrap = $(this).closest('.entry');// 日付ラッパーオブジェクト
  var d =  $(this).siblings('span.date').text();
  
  //var weeks = new Array('日','月','火','水','木','金','土');
  var now = new Date();
  /*
  var year = now.getYear();
  var month = now.getMonth() + 1;
  var day = now.getDate();
  var week = weeks[ now.getDay() ];
  */
  var hour = now.getHours();
  var min = now.getMinutes();
  var timetxt = timeSlice( hour + ':' + min );
  
  // 追加のAJAx
  $.ajax({
    type:'get',
    data:{ 'date':d, 'time':timetxt },// レコード作成時に初期入力する日付と時刻を渡す
    url:'./index_content_eat_new.php'
  })
  .done(function(dat){//alert(dat);
    //if(dat !=='1') alert('サーバーにて正常にデータセットできませんでした。');
    var res = JSON.parse(dat);
    
    //
    $.data(qDateWrap[0],'valPrev','');
    //行（と入力欄）を生成
    qDateWrap.children('.section_body').append(
      '<div class="eat_row" alt="' + res.kp + '" style="display: block;">' +
        '<span class="eatImage"></span>' +
        // 
        '<p class="time" style="float:left;">' + 
          //timetxt +
          '<input type="time" value="'+ timetxt +'" />' +
        '</p>' +
        // 削除ボタン
        '<a class="del" style="float:right;font-size:1.4em;color:#E77;"><i class="fa fa-minus-circle"></i></a>' +
        // 
        '<p class="eatName" style="clear:both;">' +
          //'<input type="text" value="" />' +
        '</p>' +
      '</div>'
    );
    // 行数を表示
    qDateWrap.find('.section_title').find('.count').text( qDateWrap.find('div.eat_row').length + '行' );
    // 入力欄にフォーカス
    qDateWrap.find('input:eq(0)').focus();
  })
  .fail(function(){
    fail();
    return;
  });
  
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 削除ボタン
$(document)
.on('click', '#content .eat_row a.del',
function(){
  
  var ret = confirm('行を削除します。');
  if(ret !== true) return;
  
  var qEatRow = $(this).closest('.eat_row');// 消そうとしている食事行オブジェクト
  var qRow = qEatRow.closest('.entry');// 日付ラッパーオブジェクト
  var keyv =  qEatRow.attr('alt');// 食事行の主キー値
  
  // 削除のAJAx
  $.ajax({
    type:'get',
    data:{ 'keyv':keyv },
    url:'./index_content_eat_del.php'
  })
  .done(function(cov){//alert(cov);
    if(cov !=='1') alert('サーバーにて正常にデータセットできませんでした。');
    
    // 行を消す
    qEatRow.remove();
    // 行数を表示
    qRow.find('.section_title').find('.count').text( qRow.find('.eat_row').length + '行' );
  })
  .fail(function(){
    fail();
    return;
  });
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 開閉ボタン
$(document)
.on('click', '#content .entry .section_title > a > i',
function(){
  // 閉じる
  if($(this).hasClass('fa-minus-square')){
    $(this).closest('.section_title').siblings('div.section_body').hide();
    $(this).removeClass('fa-minus-square').addClass('fa-plus-square').attr('title','ひらく');
  }else
  // 開く
  if($(this).hasClass('fa-plus-square')){
    $(this).closest('.section_title').siblings('div.section_body').show();
    $(this).removeClass('fa-plus-square').addClass('fa-minus-square').attr('title','たたむ');
  }else
  // 開く
  if($(this).parent('a').hasClass('add')){
    $(this).closest('.section_title').siblings('div.section_body').show();
    $(this).closest('.section_title').find('i').eq(0).removeClass('fa-plus-square').addClass('fa-minus-square').attr('title','たたむ');
  }
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 画像ボタン
$(document)
.on('click', '#content .eat_row a.pic',
function(){
  //
  var q = $(this).closest('section_body').children().eq(-1);
  $('#dragArea').appendTo(q).show();
  
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//
$(document)
  //メモ欄のフロートボックスをポップアップする
  .on('click','.ex',function(){
    $('#popupDay .memo').val('');
    var d = $(this).closest('.section_title').children('.date').text();
    $('#popupDay').find('summary .title').text(d);
    $.ajax({
      type:'get',
      data:{ 'date':d },
      url:'./index_content_day_get.php'
    })
    .done(function(dat){//alert(dat);
      //alert( dat.replace(/(\n)/g, '<br/>') );
      var j = $.parseJSON(dat);
      if(j.length >0) $('#popupDay .memo').val( decodeURIComponent(j[0].tMemo) );
    })
    .fail(function(){
      fail();
    });
    //return false;
  })
  
  //メモ欄の入力値の更新をサーバーに送信する
  .on('blur','#popupDay .memo',function(){
    var d = $('#popupDay').find('.title').text();
    var v = $(this).val();
    
    $.ajax({
      type:'get',
      data:{ 'date':d, 'col':'tMemo', 'value':v },
      url:'./index_content_day_set.php'
    })
    .done(function(cov){//alert(cov);
      if(cov !=='1') alert('サーバーにて正常にデータセットできませんでした。');
    })
    .fail(function(){
      fail();
    });
  });

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━



// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//以下はルーチン
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// indexOf
Array.prototype.ix = (function(undefined){
  var f;
  return function(val, from){
    if(from !== undefined){
      if(from < 0){
        f = from + this.length;
        if(f < 0)
          return -1;
      }else {
        f = from;
      }
    }else{
      f = 0;
    }
    for(var i=f; i<this.length; i++){
      if(this[i] === val)
        return i;
    }
    return -1;
  }
})();

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// repeat
String.prototype.repeat = function(times){
  var r;
  if(times <= 0){return '';}
  r = this.repeat(times >> 1);
  r += r;
  if(times & 1){r += this;}
  return r;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// nl2br
String.prototype.nl2br = function(){
  return this.replace(/(\r\n|\r|\n)/g, '<br />');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_数値化
var wrapInt = function(v){
  return (v===null || v==='' ? 0 : parseInt(v,10));
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 数値の表示
var dispInt = function(v){
  return (v===null || v==='' || v===0 || v==='0' ? '' : parseInt(v,10));
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_zero非表示化
var zeroOmmiter = function(qh,qm){
  qh.parent()
  .filter(function(){
    return parseInt(qh.val())===0 && parseInt(qm.val())===0;
  })
  .children('input:text').val('');
}
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_数値を2桁化
var digit2 = function(n){
  return ('0'+ n).slice(-2);
}
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_数値をHA:MA化
var timeAmount = function(n){
 if(n===null || n===0){
   return null;
 }else{
    var H= Math.floor(n /60)
       ,M= n % 60;
    return ('0'+H).slice(-2) +':'+ ('0'+M).slice(-2);
  }
}
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_時間(HA:MA:00)を数値化
var numTimeAmount = function(t){
  var arr = (t==='' ? [0,0] : t.split(':'))
     ,H = wrapInt(arr[0])
     ,M = wrapInt(arr[1]);
  return H*60+M;
}
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// 共通_時間(HA:MA:00)を(HA:MA)化
var timeSlice = function(t){
  if (t==='') return;
  var arr = t.split(':')
     ,H = wrapInt(arr[0])
     ,M = wrapInt(arr[1] ? arr[1] : 0 );
  if(H===0 && M===0) return;
  return ('0'+H).slice(-2) +':'+ ('0'+M).slice(-2);
}



// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//n日後の日付取得
var dateMove = function(date,after){
  var conms = date.getTime(); //指定の日付をミリ秒単位に変換
  after = after *1000 * 60 * 60 *24; //ミリ秒に変換
  return new Date( conms + after ); //現在＋何日後 のミリ秒で日付オブジェクト生成
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//入力値から日付オブジェクトを取得_NU new Date()
var dateAs = function(t){
  var arr = t.split('-'),
  yy = arr[0],
  mm = arr[1],
  dd = arr[2];
  return new Date(yy,wrapInt(mm)-1,dd);
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// デバイス
// @return スマホ(sp)、タブレット(tab)、その他(other)
var getDevice = function(){
  var ua = navigator.userAgent;
  if(ua.indexOf('iPhone') > 0 || ua.indexOf('iPod') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0){
    return 'sp';
  }else if(ua.indexOf('iPad') > 0 || ua.indexOf('Android') > 0){
    return 'tab';
  }else{
    return 'other';
  }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
</script>
</body>
</html>