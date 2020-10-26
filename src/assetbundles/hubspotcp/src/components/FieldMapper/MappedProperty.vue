<template>
  <tr>
    <th scope="row" class="heading-cell thin">{{ mapping.objectProperty.label }}</th>
    <td class="type-channel type-structure singleline-cell textual has-info code">
            <textarea rows="1" placeholder="Enter object template"
                      style="min-height: 36px;"
                      :value="template"
                      @input="$emit('update:template', $event.target.value)"
                      tabindex="0"></textarea>
    </td>
    <th scope="row" class="heading-cell thin">{{mapping.preview}}</th>
    <td class="thin action">
      <a class="delete icon" @click.prevent="$emit('delete')" title="Delete"></a>
    </td>
  </tr>
</template>

<script>

export default {
  props: {
    template: {
      type: String
    },
    mapping: {
      type: Object
    }
  }
};
</script>