var status = 0;
var time = 0;

function start() {
   status = 1;
   document.getElementById("startBtn").disabled = true;
   timer();
}

function stop() {
   document.getElementById("startBtn").disabled = false;
   status = 0;
}

function reset() {
   status = 0;
   time = 0;
   document.getElementById("timerLabel").innerHTML = "00:00:00";
   localStorage.setItem("saveTimeKey", time);
   document.getElementById("startBtn").disabled = false;
}

function timer() {
   if (status == 1) {
   	   setTimeout(function() {
            time = localStorage.getItem("saveTimeKey");
   	      time++;
   	      var min = Math.floor(time/100/60);
   	      var sec = Math.floor(time/100);
   	      var mSec = time % 100;

   	      if(min < 10) {
   	      	   min = "0" + min;
   	      }

   	      if (sec >= 60) {
   	      	 sec = sec % 60;
   	      }

   	      if (sec < 10) {
   	         sec = "0" + sec;

   	      }

   	      document.getElementById("timerLabel").innerHTML = min + ":" + sec + ":" + mSec;

            localStorage.setItem("saveTimeKey", time);

   	      timer();
   	      
   	   }, 10);
   }

}

var status2 = 0;

function start2() {
   var minutes = document.getElementById("timer2minutes").value;
   var seconds = document.getElementById("timer2seconds").value;
   localStorage.setItem("saveTimeMinutes", minutes);
   localStorage.setItem("saveTimeSeconds", seconds);
   status2 = 1;
   document.getElementById("startBtn2").disabled = true;
   timer2();
}

function restart2() {
   status2 = 1;
   timer2();
}

function reset2() {
   status2 = 0;
}

function timer2() {
   if (status2 == 1) {
         setTimeout(function() {
            seconds = localStorage.getItem("saveTimeSeconds");
            minutes = localStorage.getItem("saveTimeMinutes");
            seconds--;

            if (seconds <= 0) {
               minutes--;
               seconds = 60;
            }

            if (seconds < 2) {
               if (minutes == 0) {
                  var audio = new Audio('../assets/audio/timerEnd.mp3');
                  audio.volume = 0.1;
                  audio.play();
                  reset2();
               }
            }

            if (seconds < 10) {
               document.getElementById("timerLabel2").innerHTML = minutes + ":0" + seconds;
            } else {
               document.getElementById("timerLabel2").innerHTML = minutes + ":" + seconds;
            }

            localStorage.setItem("saveTimeMinutes", minutes);
            localStorage.setItem("saveTimeSeconds", seconds);

            timer2();

         }, 1000);
   } else {
      document.getElementById("startBtn2").disabled = false;
      localStorage.setItem("saveTimeMinutes", 0);
      localStorage.setItem("saveTimeSeconds", 0);
      document.getElementById("timerLabel2").innerHTML = "0:00";
   }

}


var status3 = 0;

function start3() {
   localStorage.setItem("dutyTimeMinutes", 0);
   status3 = 1;
   timer3();
}

function restart3() {
   status3 = 1;
   timer3();
}

function stop3() {
   status3 = 0;
   dutyminutes = localStorage.getItem("dutyTimeMinutes");
   if (dutyminutes > 0)
   {
      swal({
        title: "Good Job!",
        text: "You were on duty for "+dutyminutes+" minute(s).",
        icon: "success",
        button: "Ok!",
      });
   }
   localStorage.setItem("dutyTimeMinutes", 0);
}

function timer3() {
   if (status3 == 1) {
         setTimeout(function() {
            dutyminutes = localStorage.getItem("dutyTimeMinutes");

            dutyminutes++;

            localStorage.setItem("dutyTimeMinutes", dutyminutes);

            //console.log(dutyminutes);

            timer3();

         }, 60000);
   } else {
      localStorage.setItem("dutyTimeMinutes", 0);
   }

}

