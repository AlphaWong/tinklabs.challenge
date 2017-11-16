<style>
    #header {
        background-color: #666;
        height: 45px;
        text-align: left;
        margin-bottom: 15px;
        display: block;
    }
    #header a {
        color: #FFF;
        font-size: 22px;
        line-height: 45px;
        text-decoration: none;
        height: 45px;
        padding-left: 10px;
        display: block;
        float: left;
    }

    .title {
        color: #FFF;
        font-size: 32px;
        font-weight: bold;
        line-height: 45px;
        text-align: center;
        width: 200px;
        margin: auto;
        display: block;
    }
</style>

<div id="header">
    <a href="./">&lt; Back</a>
    <div class="title"><?=!empty($title)?$title:'';?></div>
    <div style="clear: both;"></div>
</div>