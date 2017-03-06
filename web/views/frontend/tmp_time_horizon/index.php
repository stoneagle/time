<?php
use yii\helpers\Html;
use app\models\Process;
use app\assets\AppAsset;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title                   = '时间管理';
$this->params['breadcrumbs'][] = $this->title;

/* $this->registerCssFile('@web/css/lib/carousel/default.css',['depends'=>['app\assets\AppAsset']]); */
/* $this->registerCssFile('@web/css/lib/carousel/normalize.css',['depends'=>['app\assets\AppAsset']]); */
/* $this->registerCssFile('@web/css/lib/carousel/styles.css',['depends'=>['app\assets\AppAsset']]); */
/* $this->registerJsFile('@web/js/lib/carousel/stopExecutionOnTimeout.js',['depends'=>['app\assets\AppAsset']]); */

/* $this->registerCssFile('@web/css/time/xxx.css',['depends'=>['app\assets\AppAsset']]); */
//$this->registerJsFile('@web/js/frontend/time/tab_carousel.js',['depends'=>['app\assets\AppAsset']]);

AppAsset::register($this);
?>
<div class="container-fluid">
    <h1><?= Html::encode($this->title); ?></h1>
    <nav class="nav nav--active">
      <ul class="nav__list">
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color1" data-letter="a"></div>
            <p class="nav__label">About</p>
          </a>
        </li>
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color2" data-letter="p"></div>
            <p class="nav__label">Products</p>
          </a>
        </li>
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color3" data-letter="q"></div>
            <p class="nav__label">Questions</p>
          </a>
        </li>
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color4" data-letter="e"></div>
            <p class="nav__label">Events</p>
          </a>
        </li>
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color5" data-letter="s"></div>
            <p class="nav__label">Sponsors</p>
          </a>
        </li>
        <li class="nav__item">
          <a href="" class="nav__link">
            <div class="nav__thumb color6" data-letter="c"></div>
            <p class="nav__label">Contact</p>
          </a>
        </li>
      </ul>
      <div class="burger burger--close">
        <div class="burger__patty"></div>
      </div>
    </nav>

    <div class="page">
      <section class="section section--active color1" data-letter="a">
        <header class="htmleaf-header">
            <h1>支持键盘控制的扁平风格水平滑动Tab选项卡 <span>A Horizontal tab menu navigation with left and right arrow key navigation</span></h1>
            <div class="htmleaf-links">
                <a class="htmleaf-icon icon-htmleaf-home-outline" href="http://www.htmleaf.com/" title="jQuery之家" target="_blank"><span> jQuery之家</span></a>
                <a class="htmleaf-icon icon-htmleaf-arrow-forward-outline" href="http://www.htmleaf.com/jQuery/Tabs/201508162423.html" title="返回下载页" target="_blank"><span> 返回下载页</span></a>
            </div>
        </header>
      </section>
      <section class="section color2" data-letter="p">
        <article class="section__wrapper">
          <h1 class="section__title">Products</h1>
          <p>Use your 'left' and 'right' arrow keys to navigate.<br> Quos vel omnis quibusdam at inventore atque assumenda dignissimos ipsa magni perferendis, minima neque saepe reprehenderit quisquam numquam voluptas quo placeat quaerat!</p>
        </article>
      </section>
      <section class="section color3" data-letter="q">
        <article class="section__wrapper">
          <h1 class="section__title">Questions</h1>
          <p>Use your 'left' and 'right' arrow keys to navigate.<br> Labore iure tempora magnam aliquid voluptatum sit placeat necessitatibus, adipisci est, ipsum doloremque. Id quia consequatur labore repellendus. Ab eligendi voluptatibus doloribus.</p>
        </article>
      </section>
      <section class="section color4" data-letter="e">
        <article class="section__wrapper">
          <h1 class="section__title">Events</h1>
         <p>Use your 'left' and 'right' arrow keys to navigate.<br> Earum porro, at odit. Dolorem velit asperiores quam obcaecati ex numquam aspernatur at et! Possimus blanditiis, distinctio est qui deleniti nisi dolorem!</p>
        </article>
      </section>
      <section class="section color5" data-letter="s">
        <article class="section__wrapper">
          <h1 class="section__title">Sponsors</h1>
          <p>Use your 'left' and 'right' arrow keys to navigate.<br> Autem alias perferendis facilis, quibusdam, ratione repellendus, voluptate officiis ipsa ullam magnam libero atque doloribus sunt est ea nisi iste porro excepturi.</p>
        </article>
      </section>
      <section class="section color6" data-letter="c">
        <div class="related">
            <h3>如果你喜欢这个插件，那么你可能也喜欢:</h3>
            <a href="http://www.htmleaf.com/html5/SVG/201505191862.html">
              <img src="related/1.jpg" width="300" alt="HTML5 SVG炫酷垂直Tabs选项卡布局特效"/>
              <h3>HTML5 SVG炫酷垂直Tabs选项卡布局特效</h3>
            </a>
            <a href="http://www.htmleaf.com/css3/animation/2014100764.html">
              <img src="related/2.jpg" width="300" alt="纯css3超酷tabs选项卡动画特效插件"/>
              <h3>纯css3超酷tabs选项卡动画特效插件</h3>
            </a>
        </div>
      </section>
    </div>
</div>

