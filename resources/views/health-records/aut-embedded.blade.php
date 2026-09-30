<section id="embedded-aut" class="bg-white border border-blue-200 rounded-lg p-6">
<h3 class="text-xl font-bold">{{ __('medical_aut.title') }}</h3>
<p class="my-3">{{ __('medical_aut.embedded_help') }}</p>
<label for="prepare-aut" class="font-semibold">
<input id="prepare-aut" type="checkbox" name="prepare_aut" value="1" v-model="autEnabled"
 @checked(old('prepare_aut'))> {{ __('medical_aut.embedded_enable') }}
</label>
<fieldset :disabled="!autEnabled" class="mt-4 disabled:opacity-60">
@include('health-records.aut-fields',['autEmbedded'=>true,'autPrefix'=>'aut_form','autDocumentName'=>'aut_documents'])
</fieldset>
</section>
