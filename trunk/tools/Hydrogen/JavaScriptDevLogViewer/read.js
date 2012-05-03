$(document).ready(function(){
  $.ajax({
    type     : "GET",
    cache    : false,
    url      : urlDevLog,
    dataType : "html",
    success  : function(text){
      lines = text.split(/\n/);
      lines.reverse();
      for(var i=0; i<lines.length; i++){
        line = lines[i];
        if(!line)
          continue;
        try{
          json = eval('('+line+')');
          json.date = new Date(json.timestamp*1000);
          json.userAgentParts	= json.userAgent.split(/ \(/);
          json.userAgentLabel	= json.userAgentParts.shift();
          json.userAgentInfo	= "("+json.userAgentParts.join("(");
          date = json.date;
          id = lines.length-i-1;
          item = $('<tr id="entry_'+id+'"></tr>');
          item.append('<td><a href="javascript:removeEntry('+id+');">x</a></td>');
          item.append('<td>'+date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDay()+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+'</td>');
          item.append('<td>'+json.url+'</td>');
          item.append('<td>'+json.message+'</td>');
          item.append('<td><acronym title="'+json.userAgentInfo+'">'+json.userAgentLabel+'</acronym></td></tr>');
          $("table tbody").append(item);
//          console.log(json);
        }catch(e){
          console.log(e);
        }
      }
      $("table.tablesorter").tablesorter({
        sortList: [[1,1]],
      });
    },
    error    : function(){
      item  = '<tr><td colspan="5"><b>AJAX Error: GET '+urlDevLog+' failed.</b><br/>Maybe there is no Log (=no Dev Messages) or Log URL is misconfigured.</td></tr>';
      $("table tbody").append(item);
    }
  });
});

function removeEntry(index){
  $.ajax({
    url : "index.php5?removeEntry="+index,
	type : "POST"
  });
  $("#entry_"+index).remove();
}