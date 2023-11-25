
var myapp = angular.module('myapp', []);

    myapp.controller('addBookingController', function($scope,$http,$filter,$timeout,$window) {
  // $scope.url="https://www.designfort.biz/mood/salon/";
 	$scope.url="https://www.mood.ae/mdadmin/salon/";
 	$scope.msgStatus='';
  $scope.msgContent='';
  $scope.timeslot_modal=$scope.staff_modal=false;
 	// $scope.url="http://localhost/mood/salon/";
 	$scope.services=$scope.eservices=$scope.selected_services=[];
  $scope.sel_services=[];
  $scope.amount=0;
 	$scope.date=$scope.service=$scope.user_id="";
  $timeout(function() 
    {
     $http({
        // url:$scope.url+"booking/services",
        url:$scope.url+"booking/services?salon_id="+$scope.salon_id,
        method:"GET",
        })

        .then(function(response)
           {
           if(response.data.status=="success")
            {
               $scope.services=response.data.services;
               $scope.users=response.data.users;
               $scope.users.forEach(function(user)
               {
                $scope.user_id=user.id;
               });
               $scope.amount=0;
            }

            else if(response.data.status=="failed")
            {
                $scope.services=[]; 
          }
          else 
          {
            $scope.services=[];
          }

         });

    });
	

    $scope.selectedServices = function(value) 
    {
      if(value.checked==true)
      {
         $scope.eservices= $filter('filter')($scope.services, {checked: true});
        $scope.eservices.forEach(function(value) 
        {
           
              if($scope.sel_services.indexOf(value.id) ==-1) 
              {
                $scope.selected_services.push({id:value.id,service:value.service,amount:value.amount});
                $scope.sel_services.push(value.id);
              }

        });
      }
      else
      {
        var index = $scope.sel_services.indexOf(value.id);
        $scope.selected_services.splice(index, 1);   
        $scope.sel_services.splice(index, 1);   
      }
      console.log($scope.sel_services);
      console.log($scope.selected_services);

    }

    $scope.staff=function(value)
    {
      // $scope.service_id=value;
      $scope.selected_service=value;
      $scope.staff_modal=true;
      $http({
      url:$scope.url+"booking/search_staffs?salon_id="+$scope.salon_id,
      params:{service_id:value},
      method:"GET",
      // withCredentials:true,
      })

      .then(function(response)
         {
         if(response.data.status=="success")
          {
             $scope.staffs=response.data.staffs;
          }

        else 
        {
          $scope.staffs=[];
          $scope.msgStatus="failed";
          $scope.msgContent="Please choose a service";
        }

       });

    };
     $scope.timeslot=function(value)
    {
      $scope.selected_service=value;
      $scope.timeslot_modal=true;
      $scope.timeframes=[];
      //  $http({
      // url:$scope.url+"booking/search_time?salon_id="+$scope.salon_id,
      // params:{service_id:$scope.service_id,staff_id:$scope.staff_id,date:$scope.date},
      // method:"GET",
      // // withCredentials:true,
      // })

      // .then(function(response)
      //    {
      //    if(response.data.status=="success")
      //     {
      //        $scope.timeframes=response.data.timeframes;
      //     }

      //   else 
      //   {
      //     $scope.timeframes=[];
      //     $scope.msgStatus="failed";
      //     $scope.msgContent=response.data.msg;
      //   }

      //  });

    };
    $scope.search_time=function(index,date,id)
    {
      console.log(index);
      console.log(id);
      $scope.selected_services.forEach(function(value) 
        {
          if(value.id==$scope.service_id)
          {
            value.date=date;
          }
        });
      console.log($scope.selected_services);
       $http({
      url:$scope.url+"booking/search_time?salon_id="+$scope.salon_id,
      params:{service_id:$scope.service_id,staff_id:$scope.staff_id,date:date},
      method:"GET",
      // withCredentials:true,
      })

      .then(function(response)
         {
         if(response.data.status=="success")
          {
             $scope.timeframes=response.data.timeframes;
          }

        else 
        {
          $scope.timeframes=[];
          $scope.msgStatus="failed";
          $scope.msgContent=response.data.msg;
        }
      });

    }
     $scope.selectStaff=function(value)
    {
      $scope.staff_id=value.id;
      $scope.staff_name=value.staff;
      $scope.service_id=value.service_id;
        $scope.selected_services.forEach(function(value) 
        {
          if(value.id==$scope.service_id)
          {
            value.staff_id=$scope.staff_id;
            value.staff_name=$scope.staff_name;

          }
        });
      // $scope.staff_modal=false;

    };
     $scope.selectTimeslot=function(value)
    {

      $scope.start_time=value.start_time;
      $scope.end_time=value.end_time;
        $scope.selected_services.forEach(function(value) 
        {
          if(value.id==$scope.service_id)
          {
            value.start_time=$scope.start_time;
            value.end_time=$scope.end_time;

          }
        });
      // $scope.timeslot_modal=false;

    };

    $scope.submit=function(user_id,amount,first_name,last_name,email,phone,address)
    {
      $scope.user_id=user_id;
      $scope.amount=amount;
      $scope.first_name=first_name;
      $scope.last_name=last_name;
      $scope.email=email;
      $scope.address=address;
      $scope.phone=phone;
       $http({
      url:$scope.url+"booking/add_booking?salon_id="+$scope.salon_id,
      params:{date:$scope.date,amount:$scope.amount,first_name:$scope.first_name,last_name:$scope.last_name,email:$scope.email,phone:$scope.phone,address:$scope.address,services:JSON.stringify($scope.selected_services)},
      method:"POST",
      // withCredentials:true,
      })

      .then(function(response)
         {
          $window.scrollTo(0, 0);
         if(response.data.status=="success")
          {
            $scope.msgStatus="success";
            $scope.msgContent=response.data.msg;
          }

        else 
        {
          $scope.timeframes=[];
          $scope.msgStatus="failed";
          $scope.msgContent=response.data.msg;
        }

       });

    };
    $scope.change=function(index)
    {
      console.log(index);
    }

});
    myapp.controller('schedulController', function($scope,$http,$filter,$timeout,$window) {
$scope.events=[];
  $scope.url="https://www.mood.ae/mdadmin/salon/schedules/view";
  $scope.burl="https://www.mood.ae/mdadmin/salon/schedules/details";
  $scope.hurl="https://www.mood.ae/mdadmin/salon/block";
  $scope.surl="https://www.mood.ae/mdadmin/salon/list_block";

  // $scope.burl="https://www.designfort.biz/mood/salon/schedules/details";

  // $scope.url="http://localhost/mood/salon/schedules/view";
  // $scope.hurl="http://localhost/mood/salon/booking/block";

  // $scope.burl="http://localhost/mood/salon/schedules/details";
  
  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();
 $timeout(function(){


   $http({
      url:$scope.url,
      method:"GET",
      params:{salon_id:$scope.salon_id,staff_id:$scope.staff_id},

      }).then(function(response)
      {


        if(response.data.error==false)
        {

          $scope.dates=response.data.booking;
          $scope.slots=response.data.slots;
          // console.log($scope.slots);

          angular.forEach($scope.dates, function(date,key) 
          {
            // $scope.events.push({title: date.title,start: date.start_date,end: date.end_date,backgroundColor:'grey'})

            $scope.events.push({title: date.title,start: date.start_date,end: date.end_date,url:$scope.burl + "?booking_id=" +date.booking_id,backgroundColor:'#6e1200'})
          });
           angular.forEach($scope.slots, function(date,key) 
          {
            // $scope.events.push({title: date.title,start: date.start_date,end: date.end_date,backgroundColor:'grey'})

            $scope.events.push({title: date.title,start: date.start_date,end: date.end_date,backgroundColor:'#b9b9b9',url:$scope.surl})
          });

        }
        else
        {
          $scope.events=[];
        }
        

      },function()
      {
        
      });


  $timeout(function()
  {
    
    var calendar = $('#calendar-drag').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      dayClick: function(date) {
      var varDate = new Date(date); //dd-mm-YYYY
      var today = new Date();
      var varDate=$filter('date')(varDate, "yyyy-MM-dd"); 
      var today=$filter('date')(today, "yyyy-MM-dd"); 
      // console.log(varDate);
      // console.log(today);
      if(varDate >= today) 
      {
        // console.log(date.format());
           $window.location.href = $scope.hurl+'?date='+date.format();
      }
      else
      {
       alert("Please choose future dates.");
      }
         },
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: $scope.events,
      timeFormat: 'h(:mm)A',
      buttonText: {
        today:    'Today',
        month:    'Month',
        week:     'Week',
        day:      'Day'
      },
      buttonIcons: { //multiple fa class because it will then output .fc-icon-fa.fa.fa-...
          prev: 'fa fa fa-angle-left',
          next: 'fa fa fa-angle-right',
          prevYear: 'fa fa fa-angle-double-left',
          nextYear: 'fa fa fa-angle-double-left'
      }
    });

  },1000);


});




// // Demo for FullCalendar with Drag/Drop external


  /* initialize the external events
  -----------------------------------------------------------------*/

  $('#external-events .external-event').each(function() {

    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
      title: $.trim($(this).text()), // use the element's text as the event title
      stick: true // maintain when user navigates (see docs on the renderEvent method)
    });

    // make the event draggable using jQuery UI
    $(this).draggable({
      zIndex: 999,
      revert: true,      // will cause the event to go back to its
      revertDuration: 0  //  original position after the drag
    });

  });


  /* initialize the calendar
  -----------------------------------------------------------------*/

  $('#calendar-external').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    defaultView: 'agendaWeek',
    editable: true,
    droppable: true, // this allows things to be dropped onto the calendar
    drop: function() {
      // is the "remove after drop" checkbox checked?
      if ($('#drop-remove').is(':checked')) {
        // if so, remove the element from the "Draggable Events" list
        $(this).remove();
      }
    },
    buttonIcons: { //multiple fa class because it will then output .fc-icon-fa.fa.fa-...
        prev: 'fa fa fa-angle-left',
        next: 'fa fa fa-angle-right',
        prevYear: 'fa fa fa-angle-double-left',
        nextYear: 'fa fa fa-angle-double-left'
    }
  });
});