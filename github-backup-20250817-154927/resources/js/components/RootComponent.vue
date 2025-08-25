<template>
  <div>
    <div style="padding-top: 64px;">
      <router-view :key="$i18n.locale" />
    </div>
  </div>
</template>

<script>
export default {
    name: "RootComponent"
};
</script>
