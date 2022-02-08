<template>
  <b-container fluid id="clients" class="content-block-width">
    <b-row>
      <b-col cols="12" class="d-flex justify-content-center text-center">
        <div>
            <h2>{{krakenBlock.subtitulo}}</h2>
            <h3>{{krakenBlock.titulo}}</h3>
        </div>
      </b-col>
    </b-row>
    <b-row class="d-flex justify-content-center flex-row">
      <b-col cols="12">
        <carousel :perPage="3">
          <slide class="p-2" v-for="(element, index) in krakenBlock.elements" :key="element.name">
            <div class="feedback shadow">
              <b-row class="site-card-header py-auto">
                <b-col cols="4">
                  <b-img fluid :src="element.avatar" alt="Responsive image"></b-img>
                </b-col>
                <b-col cols="8" class="pl-0">
                  <h3><strong>{{element.nombre}}</strong></h3>
                  <h4>{{element.linkCliente}}</h4>
                </b-col>
              </b-row>
              <b-row class="site-card-body text-center">
                <b-col>
                  <div v-bind:style="{ backgroundImage: 'url(' + element.fotoCliente + ')' }" class="bg-client-image">
                    <!--<b-img fluid 
                    src="https://previews.123rf.com/images/g215/g2152006/g215200600007/148161748-portrait-of-a-cat-in-a-smartphone-3d-stylization.jpg" 
                    alt="Responsive image"></b-img>-->
                    <h3>Testimonio</h3>
                  </div>
                </b-col>
              </b-row>
            </div>
          </slide>
          
        </carousel>
      </b-col>
    </b-row>
  </b-container>
</template>
<script>
  module.exports = {
    name: "ourcustomers-1",
    components: {
      'carousel': VueCarousel.Carousel,
      'slide': VueCarousel.Slide
    },
    data() {
      return {
        krakenBlock: [],
        settings: {
          arrows: true,
          infinite: false,
          slidesToShow: 1,
          slidesToScroll: 1,
          autoplay: false,
        },
      };
    },
  };
</script>
<style scope>#clients {
  background-color: #F8F8F8;
  padding-top: 6rem;
  padding-bottom: 6rem;
}
#clients .VueCarousel-slide {
  max-width: 425px;
}
#clients .feedback {
  width: 20vw;
  background-color: white;
  border-radius: 10px;
}
#clients .feedback .site-card-header {
  padding: 2vh;
}
#clients .feedback .site-card-header img {
  border-radius: 50% !important;
}
#clients .feedback .site-card-header h3 {
  margin-top: 1.1vh;
}
#clients .feedback .site-card-body .bg-client-image {
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  height: 45vh;
  border-radius: 0 0 15px 15px;
}
#clients .feedback .site-card-body .bg-client-image h3 {
  padding: 20px;
  background-color: rgba(0, 0, 0, 0.58);
  position: absolute;
  color: white;
  bottom: 0;
  margin-bottom: 0px;
  width: 20vw;
  border-radius: 0 0 15px 15px;
}
</style>