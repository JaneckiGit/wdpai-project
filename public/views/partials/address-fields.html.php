<?php
// Pola adresu wspólne dla „Dodaj lokal” i „Zostań gospodarzem”; formularz ma pole $form->address (AddressForm),
// a błędy adresu są pod tymi samymi nazwami pól w formularzu nadrzędnym ($form->invalidAttributes()).
$address = $form->address;
?>
<div class="field form-grid__wide">
    <label for="street">Ulica</label>
    <input type="text" id="street" name="street" value="<?= htmlspecialchars($address->street) ?>" maxlength="120" autocomplete="address-line1" placeholder="np. ul. Estery" required <?= $form->invalidAttributes('street') ?>>
</div>
<div class="field">
    <label for="building-number">Numer budynku</label>
    <input type="text" id="building-number" name="buildingNumber" value="<?= htmlspecialchars($address->buildingNumber) ?>" maxlength="10" required <?= $form->invalidAttributes('buildingNumber') ?>>
</div>
<div class="field">
    <label for="unit-number">Numer lokalu <span class="muted">(nieobowiązkowy)</span></label>
    <input type="text" id="unit-number" name="unitNumber" value="<?= htmlspecialchars($address->unitNumber) ?>" maxlength="10" <?= $form->invalidAttributes('unitNumber') ?>>
</div>
<div class="field">
    <label for="postal-code">Kod pocztowy</label>
    <input type="text" id="postal-code" name="postalCode" value="<?= htmlspecialchars($address->postalCode) ?>" maxlength="6" pattern="\d{2}-\d{3}" inputmode="numeric" autocomplete="postal-code" placeholder="00-000" required <?= $form->invalidAttributes('postalCode') ?>>
</div>
<div class="field">
    <label for="city">Miasto</label>
    <input type="text" id="city" name="city" value="<?= htmlspecialchars($address->city) ?>" maxlength="80" autocomplete="address-level2" required <?= $form->invalidAttributes('city') ?>>
</div>
