<?php
require_once 'Core/start.php';

checkIfHaveAccess();
$html_head = buildHTMLHead('Map | Incidents');

$search_bar = buildSearchbar();


echo HTMLPage($html_head, $nav_bar, $search_bar, $footer);
function HTMLPage($html_head, $nav_bar, $search_bar, $footer)
{

    $sHTML = "";
    $sHTML .= <<<HTML
	
	<!DOCTYPE html>
	
	<!--
	Author: Marck Munoz
	Date: 2016
	-->

	<html lang="en">
    $html_head
	<body>
	<div class="whs_bg">
	<div class="container">
	    $nav_bar
	    $search_bar
		<div class="page-header">
			<h1>WHS Incident Map</h1>
		</div>
		<div id="last_incident">
	        <!--last incident counter here-->
        </div>
        <p class="text-muted text-center">Notice: Hover on map blocks to see incidents for that block.</p>
		<div class="row">
		    <div class="col-md-8">
		      <img src="Images/WHS-Map.png" alt="" usemap="#WHSMap" class="img-responsive" />
                <map name="WHSMap" id="WHSMap">
                    <area class="whs_block" shape="rect" coords="973,1446,1527,1857" href="#" title="r" alt="r" />
                    <area class="whs_block" shape="poly" coords="827,969,827,969,827,969,905,951,905,814,843,814,850,733,905,733,870,733,905,733,976,733,976,812,976,846,976,884,976,927,976,959,976,978,976,998,975,1019,939,1028,923,1033,915,1035,905,1038,866,1047,723,1076,710,998,767,986,710,998,718,995,742,990,767,985,799,978,799,978,827,969" href="#" title="m" alt="m" />
                    <area class="whs_block" shape="poly" coords="349,1219,349,1191,349,1160,371,1159,380,1156,389,1156,411,1155,472,1148,509,1150,514,1091,515,1063,611,1063,611,1129,611,1148,607,1167,602,1205,602,1219,578,1227,577,1258,611,1277,611,1325,560,1324,551,1326,520,1325,485,1326,453,1325,429,1325,416,1324,396,1325,356,1327,356,1285,341,1286,341,1221,349,1219" href="#" title="l" alt="l" />
                    <area class="whs_block" shape="rect" coords="187,647,300,804" href="#" title="o" alt="o" />
                    <area class="whs_block" shape="rect" coords="187,487,275,647" href="#" title="p" alt="p" />
                    <area class="whs_block" shape="rect" coords="356,718,497,837" href="#" title="k" alt="k" />
                    <area class="whs_block" shape="rect" coords="356,622,497,718" href="#" title="j" alt="j" />
                    <area class="whs_block" shape="rect" coords="356,526,497,622" href="#" title="h" alt="h" />
                    <area class="whs_block" shape="rect" coords="356,430,497,526" href="#" title="g" alt="g" />
                    <area class="whs_block" shape="rect" coords="781,1733,905,1886" href="#" title="s" alt="s" />
                    <area class="whs_block" shape="rect" coords="683,1444,915,1552" href="#" title="n" alt="n" />
                    <area class="whs_block" shape="rect" coords="359,870,611,1047" href="#" title="b" alt="b" />
                    <area class="whs_block" shape="rect" coords="645,858,753,969" href="#" title="a" alt="a" />
                    <area class="whs_block" shape="rect" coords="542,487,723,819" href="#" alt="f" />
                    <area class="whs_block" shape="rect" coords="520,323,761,434" href="#" title="c" alt="c" />
                    <area class="whs_block" shape="rect" coords="255,117,602,270" href="#" title="d" alt="d" />
                </map>
            </div>
            <div class="col-md-4">
		        <div id="whs_map_results" style="overflow-y: scroll; height:300px;">
		            <!--WHS incident results here-->
                </div>
            </div>
        </div>
         <!-- Modal -->
         <div class="modal fade bs-example-modal-md" id="ajax_modal" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog modal-md">
              <div class="modal-content">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
                    <div class="modal-body" id="modal_body" style="overflow-y: scroll; height:400px;">
                        //Ajax results here.
                    </div>
              </div>
           </div>
         </div>
	$footer
	</div>
	</div>
	</body>
	<script>
	$(document).ready(function(){
	
        $('img[usemap="#WHSMap"]').rwdImageMaps();
        $('.whs_block').on('click', function() {
            var block = $(this).attr('alt');
                var dataString = "block_id=" + block;
                $.ajax({
                    type: 'GET',
                    url: 'JS/Ajax/ajax_whs_map.php',
                    data: dataString,
                    success: function(result) {
                       $("#ajax_modal").modal("toggle"); //On success, toggle the modal
                       $("#modal_body").html(result); //And inject the result into the modal body
                    }
                });
        });
    
        $('.whs_block').mouseenter(function() {
            var dataString = "block_id=" + $(this).attr('alt');
            $.ajax({
                type: 'GET',
                url: 'JS/Ajax/ajax_whs_map.php',
                data: dataString,
                success: function(result) {
                    $('#whs_map_results').html(result);
                }
            });
        });
        
         //Last incident counter (in days)
        $.ajax({
            type: "GET",
            url: "JS/Ajax/ajax_datediff.php",
            success: function(result){
                $('#last_incident').html(result);
            }
        });

	});
    </script>
	</html>
    
HTML;

    return $sHTML;
}
