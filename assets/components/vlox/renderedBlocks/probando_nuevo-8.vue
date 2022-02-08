  <template>
    <b-container fluid id="wellcome" class="mid-block-width">
      <b-row>
        <!--<b-col cols="3"></b-col>-->
        <b-col cols="4" class="d-flex justify-content-left flex-row">
          <div class="wellcome-text">
            <h1 v-if="krakenBlock.titulo">{{krakenBlock.titulo}}</h1>
            <!--<h1>Cirujano Maxilofacial</h1>    -->
            <p><strong>
                {{krakenBlock.subtitulo}}
              </strong>
              <br>
              <br>
              {{krakenBlock.cuerpo}}
            </p>
            <b-button :href="krakenBlock.targetBtn" pill variant="outline-primary">{{krakenBlock.textoBtn}}</b-button>
            <!--<a class="btn btn-outline-primary rounded-pill">Build yours!</a>-->
            <!--<b-modal id="modal-booking" title="Schedula a call">
                <div id="calendar"></div>
              </b-modal>-->
          </div>
        </b-col>
        <b-col cols="8">
          <b-img fluid :src="krakenBlock.imagen" alt="David Picture"></b-img>
        </b-col>
      </b-row>
    </b-container>
  </template>
  <script>
    module.exports = {
      name: "probando_nuevo-8",
      data() {
        return {
          krakenBlock: {"titulo":"arriba","subtitulo":"Conoce mas sobre david martinez, cirujano maxilofacial","cuerpo":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.","textoBtn":"Agenda una cita aqui","targetBtn":"#","imagen":"https:\/\/previews.123rf.com\/images\/g215\/g2152006\/g215200600007\/148161748-portrait-of-a-cat-in-a-smartphone-3d-stylization.jpg"}
        };
      },
    };
  </script>
  <style scope>#wellcome {
  margin-top: 7vh;
  padding-top: 15vh;
}
</style>