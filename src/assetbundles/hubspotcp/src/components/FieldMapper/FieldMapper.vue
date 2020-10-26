<template>
  <div class="flex">
    <button class="btn icon add" @click="showingPropertyPicker = !showingPropertyPicker">Add Property</button>
    <button class="btn">Refresh Properties</button>
    <button class="btn" @click.prevent="displayPreview">Preview</button>
  </div>
  <div v-if="showingPropertyPicker">
    <div class="pane flex">
      <div class="select">
      <select v-model="selectedPropertyToAdd">
        <option v-for="(item, idx) in unmappedData" :value="idx">{{item.propertyObject.label}} - ({{item.property}})</option>
      </select>
      </div>
      <div>
      <button class="btn icon add" @click.prevent="handleAddProperty">Add</button>
      </div>
    </div>
    <hr>
  </div>
  <div class="input ltr">
    <table id="sites" class="editable fullwidth">
      <thead>
      <tr>
        <th scope="col" class="heading-cell thin">HubSpot Property</th>
        <th scope="col" class="singleline-cell textual" style="">Object Template</th>
        <th scope="col" class="heading-cell thin">&nbsp;</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(mapped, idx) in mappedData">
        <th scope="row" class="heading-cell thin">{{mapped.property}}</th>
        <td class="type-channel type-structure singleline-cell textual has-info code">
            <textarea rows="1" placeholder="Enter object template"
                      @input="e => handleObjectTemplateChanged(mapped, e)"
                      style="min-height: 36px;"
                      tabindex="0">{{mapped.template}}</textarea>
        </td>
        <th scope="row" class="heading-cell thin"><button class="btn small submit" @click.prevent="saveMapping(mapped)">Save</button></th>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import api from '../../api/ecommerce.js'

export default {
  name: 'FieldMapper',
  props: {
    objectType: String
  },
  data() {
    return {
      showingPropertyPicker: false,
      propertyMappings: [],
      selectedPropertyToAdd: null
    }
  },
  mounted() {
    this.fetchMappings();
  },
  computed: {
    mappedData: function() {
      return this.propertyMappings.filter(function(item) {
        return item.id !== null
      })
    },
    unmappedData: function () {
      return this.propertyMappings.filter(function(item) {
        if (item.propertyObject === undefined) {
          console.log(item)
        }
        return item.id === null
      })
    }
  },
  methods: {
    fetchMappings() {
      api.getObjectMappings('contacts').then((res) => {
        this.propertyMappings = res.data;
      })
    },
    handleAddProperty: async function() {
      await api.saveObjectMapping(this.unmappedData[this.selectedPropertyToAdd]);
      this.fetchMappings();
    },
    handleObjectTemplateChanged(mapped, e) {
      const index = this.propertyMappings.findIndex((e) => {
        return e.id === mapped.id;
      })
      this.propertyMappings[index].template = e.target.value
    },
    async saveMapping(mapped) {
      await api.saveObjectMapping(mapped);
      this.fetchMappings();
    },
    async displayPreview() {
      const previewData = await api.getPreview(this.objectType)
      console.log(previewData)
    }
  }


}
</script>
