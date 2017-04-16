$(function(){
    $.post(pv,{id:id});
    $.post(uv,{id:id});
    $.post(ROOT+'/index.php/Similarity/calculateSimilarity',{});
    $.post(ROOT+'/index.php/Similarity/calculateNewsSimilarityByUserId',{news_id:id});
});