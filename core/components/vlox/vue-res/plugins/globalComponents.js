import Badge from "../shared/components/Badge";
//import BaseAlert from "../components/BaseAlert";
import BaseButton from "../shared/components/BaseButton";
import BookConsultation from '../shared/components/BookConsultation';

import Card from "../shared/components/Card";
import Icon from "../shared/components/Icon";

export default {
  install(Vue) {
    Vue.component(Badge.name, Badge);
    /*Vue.component(BaseAlert.name, BaseAlert);*/
    Vue.component(BaseButton.name, BaseButton);
    /*Vue.component(BaseInput.name, BaseInput);
    Vue.component(BaseCheckbox.name, BaseCheckbox);
    Vue.component(BasePagination.name, BasePagination);
    Vue.component(BaseProgress.name, BaseProgress);
    Vue.component(BaseRadio.name, BaseRadio);
    Vue.component(BaseSlider.name, BaseSlider);
    Vue.component(BaseSwitch.name, BaseSwitch);*/
    Vue.component(Card.name, Card);
    Vue.component(Icon.name, Icon);
    Vue.component(BookConsultation.name, BookConsultation);
  }
};
