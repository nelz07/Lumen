<template>
  <div>
  <multiselect 
    
    v-model="value" 
    :options="options" 
    :multiple="false" 
    group-values="data" 
    group-label="level" 
    :group-select="false" 
    :allow-empty="true"
    placeholder="Select Level" 
    track-by="name" 
    label="name"
    @input = "emitToParent"
    
    >
      <span slot="noResult">Oops! No elements found. Consider changing the search query.</span>
    </multiselect>
    <input type="hidden" name="office_id" :value="value.id" @change="emitToParent">
    
  
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
  components: {
    Multiselect
  },
  props: ['name','default_value','list_level'],
  created(){
      if(this.list_level==null){
          this.fetchAllLevels();
      }else{
          this.fetchListByLevel(this.list_level)
      }
      
      
  },
  data () {
    return {
        lists: null,
        options: [],
        value: []
    }
  },
  methods: {
    emitToParent(){
      if(this.value!=null){
        this.$emit('officeSelected', this.value);
      }
    },
    fetchAllLevels(){
      axios.get('/wApi/usr/branches')
        .then(res=>{
          this.options=res.data
            if(this.default_value!==undefined){
              
              this.options.filter( obj => {
                var item = obj.data.filter(office => {
                   office.id == this.default_value ? this.value = office : ''
                })
              })
              
          }
        })
    },
    fetchListByLevel(level){
        axios.get('/wApi/usr/branches?level='+level)
        .then(res=>{
          this.options=res.data
            if(this.default_value!==undefined){
              this.options.filter( obj => {
                var item = obj.data.filter(office => {
                   office.id == this.default_value ? this.value = office : ''
                })
              }) 
          }
        })
    }
  },
  
}

</script>
<style>
    @import "~vue-multiselect/dist/vue-multiselect.min.css";
    .multiselect__tags{
      background: #27293d;
    }
    .multiselect__input{
      background: #27293d!important;
      border-color:#2b3553
    }
    .multiselect__single{
      background: #27293d!important;
      color: white;
    }

    .is-invalid .multiselect__tags {
      border: 1px solid red !important;
    }

</style>

