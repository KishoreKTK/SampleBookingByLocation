
<center>
	<div class="alert dark alert-success alert-dismissible" role="alert" ng-if="msgStatus=='success'">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
    @{{msgContent}}
  </div>

	<div  class="alert dark alert-danger alert-dismissible" role="alert" ng-if="msgStatus=='failed'">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
     	@{{msgContent}}
 	</div>
 	
    <div class="alert dark alert-info alert-dismissible" role="alert"  ng-if="msgStatus=='warning'">
    	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
     	@{{msgContent}}
 	</div>
 	
</center>
