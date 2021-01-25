<?php
include('verifica_login.php')
?>
<html lang="en">
  <!-- # Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved. -->
  <!-- # SPDX-License-Identifier: MIT-0 -->
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>LIVE</title>

    <script src="https://player.live-video.net/1.2.0/amazon-ivs-player.min.js"></script>
  </head>
  <body>
    <div id="app">
      <div class="inner">
        <!-- Player wrapper, forcing 16:9 aspect ratio -->
        <div class="player-wrapper">
          <div class="aspect-spacer"></div>
          <div class="pos-absolute full-width full-height top-0">
            <video id="video-player" class="el-player" playsinline></video>
          </div>
        </div>

        <!-- Quiz UI -->
        <div class="quiz-wrap">
          <div id="waiting">
            <span class="waiting-text float"
              >Esperando pela próxima questão</span>
          </div>
          <div id="quiz" class="card drop">
            <div id="card-inner">
              <h2 id="question"></h2>
              <div id="answers"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </body>
</html>

<script>
const playbackUrl = "https://c9ad3fd29122.us-east-1.playback.live-video.net/api/video/v1/us-east-1.748408257262.channel.G84HonWV8BxC.m3u8";

// App
const videoPlayer = document.getElementById("video-player");
const quizEl = document.getElementById("quiz");
const waitMessage = document.getElementById("waiting");
const questionEl = document.getElementById("question");
const answersEl = document.getElementById("answers");
const cardInnerEl = document.getElementById("card-inner");

(function (IVSPlayer) {
  const PlayerState = IVSPlayer.PlayerState;
  const PlayerEventType = IVSPlayer.PlayerEventType;

  // Initialize player
  const player = IVSPlayer.create();
  player.attachHTMLVideoElement(videoPlayer);

  // Attach event listeners
  player.addEventListener(PlayerState.PLAYING, function () {
    console.log("Player State - PLAYING");
  });
  player.addEventListener(PlayerState.ENDED, function () {
    console.log("Player State - ENDED");
  });
  player.addEventListener(PlayerState.READY, function () {
    console.log("Player State - READY");
  });
  player.addEventListener(PlayerEventType.ERROR, function (err) {
    console.warn("Player Event - ERROR:", err);
  });

  player.addEventListener(PlayerEventType.TEXT_METADATA_CUE, function (cue) {
    const metadataText = cue.text;
    const position = player.getPosition().toFixed(2);
    console.log(
      `Player Event - TEXT_METADATA_CUE: "${metadataText}". Observed ${position}s after playback started.`
    );
    triggerQuiz(metadataText);
  });

  // Setup stream and play
  player.setAutoplay(true);
  player.load(playbackUrl);

  // Setvolume
  player.setVolume(0.1);

  // Remove card
  function removeCard() {
    quizEl.classList.toggle("drop");
  }

  // Trigger quiz
  function triggerQuiz(metadataText) {
    let obj = JSON.parse(metadataText);

    quizEl.style.display = "";
    quizEl.classList.remove("drop");
    waitMessage.style.display = "none";
    cardInnerEl.style.display = "none";
    cardInnerEl.style.pointerEvents = "auto";

    while (answersEl.firstChild) answersEl.removeChild(answersEl.firstChild);
    questionEl.textContent = obj.question;

    let createAnswers = function (obj, i) {
      let q = document.createElement("a");
      let qText = document.createTextNode(obj.answers[i]);
      answersEl.appendChild(q);
      q.classList.add("answer");
      q.appendChild(qText);

      q.addEventListener("click", (event) => {
        cardInnerEl.style.pointerEvents = "none";
        if (q.textContent === obj.answers[obj.correctIndex]) {
          q.classList.toggle("correct");
        } else {
          q.classList.toggle("wrong");
        }
        setTimeout(function () {
          removeCard();
          waitMessage.style.display = "";
        }, 1050);
        return false;
      });
    };

    for (var i = 0; i < obj.answers.length; i++) {
      createAnswers(obj, i);
    }
    cardInnerEl.style.display = "";
  }

  waitMessage.style.display = "";
})(window.IVSPlayer);
</script>

<style>
/* Copyright Amazon.com, Inc. or its affiliates. All Rights Reserved. */
/* SPDX-License-Identifier: MIT-0 */

/* Reset */
*,*::before,*::after{box-sizing:border-box}ul[class],ol[class]{padding:0}body,h1,h2,h3,h4,p,ul[class],ol[class],figure,blockquote,dl,dd{margin:0}html{scroll-behavior:smooth}body{min-height:100vh;text-rendering:optimizeSpeed;line-height:1.5}ul[class],ol[class]{list-style:none}a:not([class]){text-decoration-skip-ink:auto}img{max-width:100%;display:block}article>*+*{margin-top:1em}input,button,textarea,select{font:inherit}@media (prefers-reduced-motion:reduce){*{animation-duration:0.01ms!important;animation-iteration-count:1!important;transition-duration:0.01ms!important;scroll-behavior:auto!important}}

/* Variables */
:root {
  --radius: 12px;
}

/* Style */
html,
body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

body {
  overflow: hidden;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Ubuntu, "Helvetica Neue", sans-serif;
  user-select: none;
}

#app {
  background: #808080;
  height: 100%;
}

.inner {
  max-width: 1080px;
  display: flex;
  flex-direction: column;
  position: relative;
  align-items: stretch;
  margin: 0 auto;
  padding: 40px;
}

.player-wrapper {
  width: 100%;
  position: relative;
  overflow: hidden;
  transform: translate3d(0, 0, 0);
  backface-visibility: hidden;
  border-radius: var(--radius);
  box-shadow: 0 6px 30px rgba(0, 0, 0, 0.3);
  z-index: 1;
}

.aspect-spacer {
  padding-bottom: 56.25%;
}

.el-player {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  background: #000;
  border-radius: var(--radius);
}

video {
  width: 100%;
  border-radius: var(--radius);
  background: #000;
}

.quiz-wrap {
  min-height: 460px;
  position: relative;
  transition: all 0.25s ease-in;
}

.card {
  margin: 0 20px;
  padding: 20px;
  position: absolute;
  left: 0;
  right: 0;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
  transition: all 1s cubic-bezier(1, -0.56, 0, 1);
  transform: translate3d(0, 0, 0) scale(1);
  backface-visibility: hidden;
  z-index: 1;
}

.card.drop {
  opacity: 0;
  transform: translate3d(0, 200px, -20px) scale(0.92);
}

h2 {
  font-size: 25px;
  text-align: center;
  padding-bottom: 20px;
}

.answer {
  height: 50px;
  line-height: 50px;
  font-size: 20px;
  display: flex;
  text-decoration: none;
  border: 1px solid #d5dbdb;
  border-radius: 50px;
  padding: 0 24px;
  margin: 10px 0;
  background: #fafafa;
  color: #545b64;
  transition: all 0.05s ease-in-out;
}

.answer:hover {
  background: #ebebebe0;
}

.answer:active {
  background: #ff9900;
  border: 1px solid #eb5f07;
  color: #fff;
}

.answer.correct {
  background: #25a702;
  border: 1px solid #1d8102;
  color: #fff;
  animation: blink 0.45s infinite;
}

.answer.wrong {
  background: #d13212;
  border: 1px solid #b7290d;
  color: #fff;
  animation: blink 0.45s infinite;
}

#waiting {
  top: 100px;
  left: 0;
  right: 0;
  position: absolute;
  display: flex;
  align-items: center;
}

.waiting-text {
  width: 100%;
  display: block;
  text-align: center;
  font-size: 18px;
  color: #d5dbdb;
}

.float {
  transform: translateY(0px);
  animation: float 6s ease-in-out infinite;
}

/* Utility - Position */
.pos-absolute {
  position: absolute !important;
}
.pos-fixed {
  position: fixed !important;
}
.pos-relative {
  position: relative !important;
}
.top-0 {
  top: 0 !important;
}
.bottom-0 {
  bottom: 0 !important;
}

/* Utility - Width/Height */
.full-width {
  width: 100%;
}
.full-height {
  height: 100%;
}

/* Animations */
@keyframes blink {
  50% {
    opacity: 0.8;
  }
}

@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
  100% {
    transform: translateY(0px);
  }
}

/* Mediaqueries */
@media (max-width: 767px) {
  h2 {
    font-size: 20px;
  }
  .card {
    top: -20px;
  }
}

@media (min-width: 767px) {
  .card {
    top: -25%;
  }
}

</style>