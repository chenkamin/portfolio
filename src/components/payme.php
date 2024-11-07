outbound

<?php
?>
<!doctype html>
<html <?php
      if ($langcode_browser == "he") {
        echo 'lang="he" dir="rtl"';
      } else {
        echo 'lang="en" dir="ltr"';
      }
      ?>>

<head>
  <meta charset="UTF-8">
  <!-- <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"> -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.payme.io/hf/v1/hostedfields.js"></script>
</head>

<body>
  <link rel="stylesheet" href="<?= $companyterminalurl . '/payme/style.css'; ?>">
  <script language="JavaScript" type="text/javascript" src="https://js.paymentsos.com/v2/0.0.1/token.min.js"></script>
  <script>
    POS.setPublicKey("<?= $publickKey; ?>");
    POS.setEnvironment("<?= $appEnv; ?>");
  </script>

  <?php
  include_once "helpers.php";

  function handleLabelsLang($string, $lang)
  {
    if ($lang == "he") {
      return $string;
    } else {
      $output = str_replace("-", "", $string);
      return strtoupper($output);
    }
  }

  function handleBtnText($string,$lang)
  {
    if ($lang == "he") {
      return "אישור ו".$string;
    } else {
      return strtoupper($string);
    }
  }

  function truncateString($string, $length = 31)
  {
    if (strlen($string) <= $length) {
      return $string;
    }
    return substr($string, 0, $length) . '...';
  }


  $orderId = $bookingref . '-' . $pnrpid;
  $countries = json_decode(file_get_contents("cellulant/countries.json"), true);
  $db = new DBsession('africainv');
  $connectPnr = DBcommand::ExecuteQuery($db, 'select  top 1 invPNRlinked from inv where invpnrid= ? and invCompanyID = ?', array($pnrid, $companyid));
  if(!empty($connectPnr[0]["invpnrlinked"])){
    $pnrid = $connectPnr[0]['invpnrlinked'];
  }
  $pax = DBcommand::ExecuteQuery($db, 'select paxfirstname,paxlastname,paxemail,paxcontact from pax where paxpnrid = ?  and paxCompanyID = ? order by paxid asc', array($pnrid, $companyid));
  $firstName = $pax[0]["paxfirstname"];
  $lastName = $pax[0]["paxlastname"];
  $email = $pax[0]["paxemail"];
  $phone = $pax[0]["paxcontact"];
  $rtMap = [
    "ILS" => "₪",
    "USD" => "$",
    "EUR" => "€"
  ];



  $rtText = $rtMap[$rtname];
  $rtTextHE = $langcode_browser == "he" ? $rtText : "";
  $rtTextEN = $langcode_browser == "en" ? $rtText : "";

  DBcommand::ExecuteQuery($db, 'exec PAYMENT_updatepnrp @pnrpid=?,@pnrpamount=?,@pnrprtid=?,@pnrpstatus=?', array($pnrpid, $pnrtotal, $terminalrtid, 'Created'));

  ?>
  <div>&nbsp;</div>
  <div id="loader-overlay" style="position: fixed;top: 0;left: 0;width: 100%;height: 100%;background-color: rgba(0, 0, 0, 0.3);z-index: 9999;display: none;justify-content: center;align-items: center;">
    <img src="./payme/loader.gif" alt="Loading...">
  </div>




  <div class="container" style="font-family: 'Open Sans', sans-serif;">

    <div class="row">
      <div class="col-xs-12 col-sm-8 col-sm-offset-2">
        <div class="panel panel-default credit-card-box">
          <div class="panel-body">
            <form role="form" id="checkout-form">
              <div id="dynamic-form-content"></div>
              <div class="row " id="names">

              </div>
              <div class="row">
                <div class="col-xs-7 col-md-6 internal-inputs">
                  <label for="country-container" class="control-label"><?= handleLabelsLang($words[$langcode_browser]['words']['w_country'], $langcode_browser) ?></label>
                  <select class="form-control" id="phone_extension" style="height:48;border-radius: 3px !important;border-color:#dcdcdc !important">
                    <option value='israel'>Israel</option>
                    <?php foreach ($countries as $key => $vc) { ?>
                      <option style="padding-left:6px !important;padding-right:6px !important;" title='<?= $vc['name'] ?>' value='<?= $vc['name'] ?>'><?= truncateString($vc['name']) ?></option>
                    <?php } ?>
                  </select>
                  <p id="country-messages" class="help-block" style="display: none;"></p>
                </div>

                <div class="col-xs-7 col-md-6 internal-inputs">
                  <div class="form-group" id="email-group">
                    <label for="email-container" class="control-label"><?= handleLabelsLang($words[$langcode_browser]['words']['w_email'], $langcode_browser) ?></label>
                    <div>
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="email-field" inputmode="text" autocomplete="cc-given-name" value='<?php echo $email; ?>'>
                    </div>
                    <p id="email-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-7 col-md-6 internal-inputs">
                  <div class="form-group" id="phone-group">
                    <label for="phone-container" class="control-label"><?= handleLabelsLang($words[$langcode_browser]['words']['w_phone'], $langcode_browser); ?></label>
                    <div>
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="phone-field" inputmode="text" autocomplete="cc-given-name" value='<?php echo $phone; ?>'>
                    </div>
                    <p id="phone-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>
                <div class="col-xs-7 col-md-6 internal-inputs" id="social-con">
                  <div class="form-group" id="social-id-group">
                    <label for="social-id-container" class="control-label" id="social-id-label"></label>
                    <div>
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="social-field" autocomplete="social-field" inputmode="text" maxlength="9">
                    </div>
                    <p id="social-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>
                <div class="col-xs-7 col-md-6 internal-inputs" id="zip-con">
                  <div class="form-group" id="zip-code-group">
                    <label for="zip-code-container" class="control-label"><?= handleLabelsLang($words[$langcode_browser]['words']['w_itinerary_zip'], $langcode_browser); ?></label>
                    <div>
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="zip-field" inputmode="text">
                    </div>
                    <p id="zip-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>
              </div>
              <div class="row" style="display:none;" id="checkout-form-errors">
                <div class="col-xs-12">
                  <p class="payment-errors"></p>
                </div>
              </div>
              <!-- margin-left:0 !important;margin-right:0 !important -->

              <div class="row row-submit internal-inputs">
                <button class="subscribe btn  btn-lg " id="submit-button" style="background-color:#041e42;border-color:#041e42; ; margin-left: auto; margin-right: auto;">
                  <?= $rtTextHE; ?><?= handleBtnText($words[$langcode_browser]['words']['w_pay_topup'], $langcode_browser) ?> <?= $rtTextEN; ?><?= $pnrtotal; ?>
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <script>
    // HELPERS ---------------------------------------------------------------------------------
    var isSocialId = true
    const lang = '<?= $langcode_browser ?>'; // Get the language code dynamically

    document.getElementsByClassName("progressBar_holder")[0].style.display = "none";
    const requiredErrorMap = {
      he: "גם את זה בבקשה",
      en: "Field is required"
    }

    const invalidDataMap = {
      he: "משהו לא תקין, כדאי לנסות שוב",
      en: "Invalid data"
    }


    const expirationMap = {
      he: "תוקף",
      en: "EXPIRATION DATE"
    }

    const firstNameMap = {
      he: "שם פרטי באנגלית של בעל/ת הכרטיס",
      en: "FIRST NAME"
    }

    const lastNameMap = {
      he: "שם משפחה באנגלית של בעל/ת הכרטיס",
      en: "LAST NAME"
    }

    const cardTextMap = {
      he: "מספר כרטיס אשראי",
      en: "CARD NUMBER"
    }

    const socialIdMap = {
      he: "תעודת זהות של בעל/ת הכרטיס",
      en: "ID NUMBER (9 DIGITS)"
    }

    const phoneMap = {
      he: "טלפון נייד",
      en: "PHONE NUMBER"
    }

    const attemptsMap = {
      he: "הבחנו במספר ניסיונות לא מוצלחים לעבד את התשלום שלך. למען אבטחתך, השהינו באופן זמני ניסיונות תשלום נוספים. תודה על ההבנה והסבלנות",
      en: "We noticed multiple unsuccessful attempts to process your payment. For your security, we have temporarily paused further payment attempts. Thank you for your understanding and patience"
    }

    const unExpectedErrorMap = {
      he: "אירעה שגיאה בעת עיבוד התשלום. אנא נסה שוב מאוחר יותר",
      en: "An unexpected error occurred during payment processing. Please try again later"
    }

    const tokenErrorMap = {
      he: "לא הצלחנו לאמת את פרטי הכרטיס. כדאי לבדוק שהם נכונים ולנסות שוב",
      en: "Your payment details could not be verified. Check your details and try again"
    }


    function getExpirationText() {
      return expirationMap[lang];
    }

    function getFirstNameText() {
      return firstNameMap[lang];
    }

    function getTokenErrorText() {
      return tokenErrorMap[lang];
    }

    function getLastNameText() {
      return lastNameMap[lang];
    }
    

    function getCardText() {
      return cardTextMap[lang];
    }

    function getSocialIdText() {
      return socialIdMap[lang];
    }

    function getPhoneText() {
      return phoneMap[lang];
    }



    function getUnexpectedError() {
      return unExpectedErrorMap[lang];
    }


    function getAttempsError() {
      return attemptsMap[lang];

    }

    document.addEventListener("DOMContentLoaded", function() {
      // CHECK FOR FOCUS ON MOBILE

      const dynamicFormContent = document.getElementById("dynamic-form-content");
      const dynamicNamesOrder = document.getElementById("names");
      const cardNumberGroup = `
        <div class="col-md-6 col-xs-7">
            <div class="form-group" id="card-number-group">
                <label for="card-number-container" class="control-label">${getCardText()}</label>
                <div class="input-group input-group-lg">
                    <div id="card-number-container" class="form-control input-lg"></div>
                    <span class="input-group-addon"><i class="fa fa-credit-card" id="card-provider"></i></span>
                </div>
                <p id="card-number-messages" class="help-block" style="display: none;"></p>
            </div>
        </div>`;

      const cardExpirationGroup = `
        <div class="col-md-4 col-xs-7 exp-cvv-con-item">
            <div class="form-group" id="card-expiration-group">
                <label for="card-expiration-container" class="control-label">${getExpirationText()}</label>
                <div id="card-expiration-container" class="form-control input-lg"></div>
                <p id="card-expiration-messages" class="help-block" style="display: none;"></p>
            </div>
        </div>`;

      const cardCVVGroup = `
        <div class="col-md-2 col-xs-7 exp-cvv-con-item">
            <div class="form-group" id="card-cvv-group">
                <label for="card-cvv-container" class="control-label">CVV</label>
                <div id="card-cvv-container" class="form-control input-lg"></div>
                <p id="card-cvv-messages" class="help-block" style="display: none;"></p>
            </div>
        </div>`;

      const firstNameCon = `<div class="col-xs-7 col-md-6 internal-inputs">
                  <div class="form-group" id="first-name-group">
                    <label for="first-name-container" class="control-label">${getFirstNameText()}</label>
                    <div id="first-name-container">
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="first-name-field" inputmode="text" autocomplete="cc-given-name" value='<?php echo $firstName; ?>'>
                    </div>
                    <p id="first-name-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>`;

      const lastNameCon = ` <div class="col-xs-7 col-md-6 internal-inputs">
                  <div class="form-group" id="last-name-group">
                    <label for="last-name-container" class="control-label">${getLastNameText()}</label>
                    <div>
                      <input style="padding:10px;margin-top:2px; outline: none;" class="base valid" type="text" id="last-name-field" inputmode="text" autocomplete="cc-given-name" value='<?php echo $lastName; ?>'>
                    </div>
                    <p id="last-name-messages" class="help-block" style="display: none;"></p>
                  </div>
                </div>`;

      const expCvvContainer = `
        <div class="exp-cvv-con">
            ${cardExpirationGroup}
            ${cardCVVGroup}
        </div>`;

      if (lang === 'he') {
        const expCvvContainer = `
            <div class="exp-cvv-con">
            ${cardCVVGroup}
            ${cardExpirationGroup}
            </div>`;
        if (window.innerWidth > 500) {
          dynamicFormContent.innerHTML = `<div class="row">${expCvvContainer}${cardNumberGroup}</div>`;
          dynamicNamesOrder.innerHTML = `${lastNameCon}${firstNameCon}`;
        } else {
          dynamicFormContent.innerHTML = `<div class="row">${cardNumberGroup}${expCvvContainer}</div>`;
          dynamicNamesOrder.innerHTML = `${firstNameCon}${lastNameCon}`;
        }

      } else if (lang === 'en') {
        const expCvvContainer = `<div class="exp-cvv-con">${cardExpirationGroup}${cardCVVGroup}</div>`;
        dynamicFormContent.innerHTML = `<div class="row">${cardNumberGroup}${expCvvContainer}</div>`;
        dynamicNamesOrder.innerHTML = `${firstNameCon}${lastNameCon}`;
      }


      if (lang == 'he') {
        const labels = document.querySelectorAll('.control-label');
        labels.forEach(function(label) {
          label.style.direction = 'rtl';
        });

        const errors = document.querySelectorAll('.help-block');
        errors.forEach(function(error) {
          error.style.direction = 'rtl';
        });
        const inputs = document.querySelectorAll('input');
        inputs.forEach(function(error) {
          error.style.direction = 'rtl';
        });
        const select = document.querySelectorAll('select');
        select.forEach(function(error) {
          error.style.direction = 'rtl';
        });
      }

      updateLabelContent()

      document.getElementById('phone_extension').addEventListener('change', function() {
        var country = this.value;
        var zipGroup = document.getElementById('zip-con');
        var socialIdGroup = document.getElementById('social-con');

        if (country.toLowerCase() === 'israel') {
          zipGroup.style.display = 'none';
          socialIdGroup.style.display = 'block';
          isSocialId = true
        } else {
          isSocialId = false
          zipGroup.style.display = 'block';
          socialIdGroup.style.display = 'none';
        }
      });

      document.getElementById('phone_extension').dispatchEvent(new Event('change'));
      const orderId = '<?= $bookingref . '-' . $pnrpid; ?>';
      const pnrtotal = '<?= $pnrtotal; ?>';
      const terminalrtid = '<?= $terminalrtid; ?>';
      const bookingengineurl = '<?= $bookingengineurl; ?>';
      const pnrunq = '<?= $pnrunq; ?>';
      const rtname = '<?= $rtname; ?>';
      const rtid = '<?= $rtid; ?>';
      const isTestMode = <?= $companyterminalvar5; ?>;
      const errorRedirect = bookingengineurl + '/booking/' + pnrunq + '/error'

      let token = '';

      const apiKey = '<?= $companyterminalvar1; ?>';
      const mpl = '<?= $companyterminalvar2; ?>';

      const consolePre = document.getElementById('console-pre');
      const form = document.getElementById('checkout-form');
      const cardProvider = document.getElementById('card-provider');

      const numberGroup = document.getElementById('card-number-group');
      const numberMessages = document.getElementById('card-number-messages');

      const expirationGroup = document.getElementById('card-expiration-group');
      const expirationMessages = document.getElementById('card-expiration-messages');

      const cvcGroup = document.getElementById('card-cvv-group');
      const cvcMessages = document.getElementById('card-cvv-messages');

      const firstNameGroup = document.getElementById('first-name-group');
      const firstNameMessages = document.getElementById('first-name-messages');

      const lastNameGroup = document.getElementById('last-name-group');
      const lastNameMessages = document.getElementById('last-name-messages');

      const emailGroup = document.getElementById('email-group');
      const emailMessages = document.getElementById('email-messages');

      const phoneGroup = document.getElementById('phone-group');
      const phoneMessages = document.getElementById('phone-messages');

      const socialIdGroup = document.getElementById('social-id-group');
      const socialIdMessages = document.getElementById('social-id-messages');

      const zipCodeGroup = document.getElementById('zip-code-group');
      const zipCodeMessages = document.getElementById('zip-code-messages');




      function updateLabelContent() {
        const socialIdLabel = document.querySelector('#social-id-group .control-label');
        const phoneLabel = document.querySelector('#phone-group .control-label');

        const socialId = getSocialIdText();
        const phone = getPhoneText();
        phoneLabel
        if (socialIdLabel) {
          socialIdLabel.textContent = socialId;
        }
        if (phoneLabel) {
          phoneLabel.textContent = phone;
        }
      }


      // -----------------------------------------------------------------------------------------------------------------

      function clearError(elementId) {
        const errorElement = document.getElementById(elementId);
        errorElement.innerText = '';
        errorElement.style.display = 'none';

      }

      const submitButton = document.getElementById('submit-button');
      submitButton.disabled = true;

      function getRequiredError() {
        return requiredErrorMap[lang];
      }

      function getInvalidDataError() {
        return invalidDataMap[lang];
      }



      function toggleValidationCard(wrapper, ev) {
        clearError("card-number-messages")
        if (ev.isValid) {
          this.style.display = 'none';
          wrapper.classList.remove('has-error');
        } else {

          this.innerText = ev.message == "Mandatory field" ? getRequiredError() : getInvalidDataError();
          this.style.display = 'block';
          wrapper.classList.add('has-error');
        }
      }

      function toggleValidationExp(wrapper, ev) {
        clearError("card-expiration-messages")
        if (ev.isValid) {
          this.style.display = 'none';
          wrapper.classList.remove('has-error');
        } else {
          this.innerText = ev.message == "Mandatory field" ? getRequiredError() : getInvalidDataError();

          this.style.display = 'block';
          wrapper.classList.add('has-error');
        }
      }

      function toggleValidationCvv(wrapper, ev) {
        clearError("card-cvv-messages")
        if (ev.isValid) {
          this.style.display = 'none';
          wrapper.classList.remove('has-error');
        } else {
          this.innerText = ev.message == "Mandatory field" ? getRequiredError() : getInvalidDataError();
          this.style.display = 'block';
          wrapper.classList.add('has-error');
        }
      }

      function changeCardProviderIcon(cardVendor) {
        const vendorsToClasses = {
          'unknown': ['fa', 'fa-credit-card'],
          'amex': ['fa', 'fa-cc-amex'],
          'diners': ['fa', 'fa-cc-diners-club'],
          'jcb': ['fa', 'fa-cc-jcb'],
          'visa': ['fa', 'fa-cc-visa'],
          'mastercard': ['fa', 'fa-cc-mastercard'],
          'discover': ['fa', 'fa-cc-discover'],
        };

        cardProvider.classList.remove(...cardProvider.classList);
        cardProvider.classList.add(...(vendorsToClasses[cardVendor] ? vendorsToClasses[cardVendor] : vendorsToClasses['unknown']));
      }

      // -----------------------------------------------------------------------------------------------------------------

      const allFieldsReady = [];



      PayMe.create(apiKey, {
        testMode: isTestMode,
        language: lang

      }).then((instance) => {

        const fields = instance.hostedFields();
        var hostedFieldsCss = {
          styles: {
            base: {
              'font-size': '16px',
            },
            invalid: {
              'font-size': '16px'
            },
            valid: {
              'font-size': '16px'
            }
          }
        };
        const cardNumber = fields.create(PayMe.fields.NUMBER, hostedFieldsCss);
        allFieldsReady.push(
          cardNumber.mount('#card-number-container')
        );
        cardNumber.on('card-type-changed', ev => changeCardProviderIcon(ev.cardType));
        cardNumber.on('keyup', toggleValidationCard.bind(numberMessages, numberGroup));

        const expiration = fields.create(PayMe.fields.EXPIRATION,hostedFieldsCss);
        allFieldsReady.push(
          expiration.mount('#card-expiration-container')
        );
        expiration.on('keyup', toggleValidationExp.bind(expirationMessages, expirationGroup));
        expiration.on('validity-changed', toggleValidationExp.bind(expirationMessages, expirationGroup));

        const cvc = fields.create(PayMe.fields.CVC,hostedFieldsCss);
        allFieldsReady.push(
          cvc.mount('#card-cvv-container')
        );
        cvc.on('keyup', toggleValidationCvv.bind(cvcMessages, cvcGroup));
        cvc.on('validity-changed', toggleValidationCvv.bind(cvcMessages, cvcGroup));

        Promise.all(allFieldsReady).then(() => submitButton.disabled = false);
        const errorElement = document.querySelector('.payment-errors');
        const errorContainer = document.getElementById('checkout-form-errors');

        function disableSubmit() {
          const submitButton = document.getElementById('submit-button');
          submitButton.disabled = true;
        }

        function enableSubmit() {
          const submitButton = document.getElementById('submit-button');
          submitButton.disabled = false;
        }
        function hideLoader(enable = false) {
          const loaderOverlay = document.getElementById('loader-overlay');
          loaderOverlay.style.display = 'none'; // Hide the loader overlay
          if (enable) {
            enableSubmit();
          }
        }

        function showLoader() {
          const loaderOverlay = document.getElementById('loader-overlay');
          loaderOverlay.style.display = 'flex'; // Show the loader overlay
          disableSubmit();
        }


        function showSubmitError(error) {
          errorElement.innerText = error || getUnexpectedError();
          errorElement.style.color = 'white';
          // errorElement.style.width = '100%';
          errorElement.style.padding = '10px';
          errorElement.style.marginTop = '10px';
          errorElement.style.border = '1px solid red';
          errorElement.style.backgroundColor = '#a94442';
          errorElement.style.borderRadius = '5px';
          errorElement.style.fontSize = '14px';
          errorElement.style.textAlign = 'center';
          errorContainer.style.display = 'block';
          hideLoader(true)


        }

        function removeSubmitError() {
          errorContainer.style.display = 'none';
        }

        function validateSocialId(socialId) {
          const socialIdPattern = /^\d{9}$/;
          return socialIdPattern.test(socialId);
        }

        function validateZip(zip) {
          const zipPattern = /^\d{1,10}$/;
          return zipPattern.test(zip);
        }

        function validateName(name) {
          const namePattern = /^[a-zA-Z' ]{1,30}$/;
          return namePattern.test(name);
        }

        function validateEmail(email) {
          const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,30}$/;
          return emailPattern.test(email);
        }

        function validatePhoneNumber(phone) {
          const phonePattern = /^\d{8,13}$/;
          return phonePattern.test(phone);
        }

        function showError(elementId, message) {
          const errorElement = document.getElementById(elementId);
          errorElement.innerText = message;
          errorElement.style.color = '#a94442';
          errorElement.style.width = '100%';
          errorElement.style.fontSize = '14px';
          // errorElement.style.height = '14px';
          errorElement.style.display = 'flex';
        }

        function clearError(elementId) {
          const errorElement = document.getElementById(elementId);
          errorElement.innerText = '';
          errorElement.style.display = 'none';
        }

        document.getElementById('first-name-field').addEventListener('input', () => clearError('first-name-messages', 'first-name-field'));
        document.getElementById('last-name-field').addEventListener('input', () => clearError('last-name-messages', 'last-name-field'));
        document.getElementById('email-field').addEventListener('input', () => clearError('email-messages', 'email-field'));
        document.getElementById('phone-field').addEventListener('input', () => clearError('phone-messages', 'phone-field'));
        document.getElementById('social-field').addEventListener('input', () => clearError('social-messages', 'social-field'));
        document.getElementById('zip-field').addEventListener('input', () => clearError('zip-messages', 'zip-field'));


        function handleCardErrors(errors) {
          const errorMapping = {
            'cardNumber': 'card-number-messages',
            'cardExpiration': 'card-expiration-messages',
            'cvc': 'card-cvv-messages'
          };

          const errorCodeMapping = {
            cardNumber: {
              "he": "מספר כרטיס אשראי לא תקין",
              "en": "Invalid credit card number"
            },
            cardExpiration: {
              "he": "תוקף לא תקין",
              "en": "invalid expiration"
            },
            cvc: {
              "he": "cvv לא תקין",
              "en": "invalid cvv"
            }
          };


          for (const key in errors) {
            if (errors.hasOwnProperty(key) && errorMapping[key]) {
              const errorMessage = errors[key];
              if (errorMessage === "Mandatory field") {
                showError(errorMapping[key], getRequiredError());
              } else {
                showError(errorMapping[key], errorCodeMapping[key][lang]);
              }

            }
          }

        }

        function handleAttempts(response) {
          let attempts = parseInt(localStorage.getItem('attempts')) || 0;
          attempts += 1;
          localStorage.setItem('attempts', attempts);

          if (attempts >= 5) {
            showSubmitError(getAttempsError());

            localStorage.removeItem('attempts');
            setTimeout(() => {
               window.location.href = response.redirecturl;
            }, 3000);
            hideLoader(true)

            return;
          }
          showSubmitError(response.message);

        }

        function handleTokenization(sale) {
          try {
            instance.tokenize(sale)
              .then(data => {
                requestData["buyer_key"] = data.token;
                const isValid = validateForm(isSocialId);

                if (!isValid) {
                  hideLoader(true)
                  return;
                }


                sendRequest(requestData);
              })
              .catch(err => {
                if (err.errors) {
                  handleCardErrors(err.errors);
                } else {
                  const isValid = validateForm(isSocialId);
                  if (!isValid) {
                    hideLoader(true)
                    return;
                  }
                  handleAttempts({
                    message: getTokenErrorText(),
                    redirecturl: errorRedirect
                  })
                }
                hideLoader(true)
              });
          } catch (err) {
            showSubmitError(err)
          }
        }

        function sendRequest(requestData) {
          fetch('payme/process.php', {
              method: 'POST',
              headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              },
              body: JSON.stringify(requestData)
              }).then(response => response.text())  
              .then(text => {
                let cleanedText = text.replace(/^(\s*(\[\]|\{\})\s*)*/, '');
                let data = JSON.parse(cleanedText);
                return data
              })
            .then(data => handleResponse(data))
            .catch(error => {
              showSubmitError(getUnexpectedError());
            });
        }

        function handleResponse(response) {
          if (response.status === 'success') {
            localStorage.removeItem('attempts');
            hideLoader(false);
            window.location.href = response.redirecturl;
          } else {
            handleAttempts(response);
          }
        }


        const requestData = {
          sale_price: pnrtotal,
          product_name: "flight",
          currency: rtname == 'NIS' ? 'ILS' : rtname,
          rtid: rtid,
          buyer_key: '',
          seller_payme_id: mpl,
          orderId: orderId,
          pnrtotal: pnrtotal,
          terminalrtid: terminalrtid,
          bookingengineurl: bookingengineurl,
          pnrunq: pnrunq,
          lang: lang,
        };

        function validateForm(isSocialId) {
          const firstName = document.getElementById('first-name-field').value;
          const lastName = document.getElementById('last-name-field').value;
          const email = document.getElementById('email-field').value;
          const phone = document.getElementById('phone-field').value;
          const socialField = document.getElementById('social-field').value;
          const zipField = document.getElementById('zip-field').value;

          let isValid = true;

          if (!firstName) {
            showError('first-name-messages', getRequiredError());
            isValid = false;
          } else if (!validateName(firstName)) {
            showError('first-name-messages', getInvalidDataError());
            isValid = false;
          } else {
            clearError('first-name-messages', 'first-name-field');
          }

          if (!lastName) {
            showError('last-name-messages', getRequiredError());
            isValid = false;
          } else if (!validateName(lastName)) {
            showError('last-name-messages', getInvalidDataError());
            isValid = false;
          } else {
            clearError('last-name-messages', 'last-name-field');
          }

          if (!email) {
            showError('email-messages', getRequiredError());
            isValid = false;
          } else if (!validateEmail(email)) {
            showError('email-messages', getInvalidDataError());
            isValid = false;
          } else {
            clearError('email-messages', 'email-field');
          }

          if (!phone) {
            showError('phone-messages', getRequiredError());
            isValid = false;
          } else if (!validatePhoneNumber(phone)) {
            showError('phone-messages', getInvalidDataError());
            isValid = false;
          } else {
            clearError('phone-messages', 'phone-field');
          }

          if (isSocialId) {
            if (!socialField) {
              showError('social-messages', getRequiredError());
              isValid = false;
            } else if (!validateSocialId(socialField)) {
              showError('social-messages', getInvalidDataError());
              isValid = false;
            } else {
              clearError('social-messages', 'social-field');
            }
          } else {
            if (!zipField) {
              showError('zip-messages', getRequiredError());
              isValid = false;
            } else if (!validateZip(zipField)) {
              showError('zip-messages', getInvalidDataError());
              isValid = false;
            } else {
              clearError('zip-messages', 'zip-field');
            }
          }

          return isValid;
        }


        form.addEventListener('submit', ev => {
          try {
            removeSubmitError();
            ev.preventDefault();
            showLoader();
            const firstName = document.getElementById('first-name-field').value;
            const lastName = document.getElementById('last-name-field').value;
            const email = document.getElementById('email-field').value;
            const phone = document.getElementById('phone-field').value;
            const socialField = document.getElementById('social-field').value;
            const zipField = document.getElementById('zip-field').value;
            const isValid = validateForm(isSocialId);

            const sale = {
              payerFirstName: document.getElementById('first-name-field').value,
              payerLastName: document.getElementById('last-name-field').value,
              payerEmail: document.getElementById('email-field').value,
              payerPhone: document.getElementById('phone-field').value,
              payerSocialId: document.getElementById('social-field').value,
              payerZipCode: document.getElementById('zip-field').value,
              total: {
                label: 'Flight',
                amount: {
                  currency: rtname,
                  value: pnrtotal,
                }
              }
            };

            if (!requestData["buyer_key"]) {
              handleTokenization(sale)
            } else {
              const isValid = validateForm(isSocialId);
              if (!isValid) {
                hideLoader(true)
                return;
              }
              sendRequest(requestData);
            }
          } catch (err) {
            showSubmitError(err)
          }
        });
      });
    });
  </script>
</body>

</html>


//listener:
<?php
include_once "../sys/sessionsettings.php";
include_once "helpers.php";

try {
  $rawInput = file_get_contents('php://input');

  parse_str($rawInput, $parsedData);

  $status = $parsedData['status_code'];
  $merchantReference = $parsedData['payme_sale_id'];
  $amount = $parsedData['price'] / 100;
  if (!isset($parsedData['transaction_id'])) {
    http_response_code(500);
    echo json_encode('Transaction id is not set');
    exit;
  }

  $transactionId = explode('-', $parsedData['transaction_id']);
  $pnrpid = $transactionId[1];

  $db = new DBsession('africainv'); //Session obj creation.
  $data = DBcommand::ExecuteQuery($db, 'exec [PAYMENT_pullpnrp] @pnrpid=?', [$pnrpid]);
  $pnrunq = $data[0]['pnrunq'];
  $pnrid = $data[0]['pnrid'];
  $pnrpresmsg = $data[0]['pnrpresmsg'];
  $paymentfromcrs = 0;
  $companyterminalid = 0;
  $paymentdone = false;
  $gatewayname = 'payme';
  $paymentapproval = $merchantReference;
  $RCPTPid = 0;

  include_once '../sys/getinfo.php';
  if ($status == 0) {
    $url = $companyterminalvar4.'/api/get-transactions';
    $body = [
        'seller_payme_id' => $companyterminalvar2,
        'sale_payme_id' => $merchantReference,
    ];
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['items'][0])) {
        $item = $responseData['items'][0];
        if ($item['sale_status'] === 'completed') {

          $db = new DBsession('africainv');
          $paymentfromcrs = 0;
          $gatewayname = 'payme';
          $paymentdone = true;

          $rtname = $item['sale_currency'];
          $paymentdata = $item['sale_buyer_details']["buyer_card_brand"] . "-" .substr($item['sale_buyer_details']["buyer_card_mask"], -4);
          $rcptCardHolderID = $item['sale_buyer_details']['buyer_social_id'];
          $rcptCardHolder = $item['sale_buyer_details']['buyer_name'];
          $RCPTPid = 0;
          $paymentamount = $amount;

          if ($paymentstatus == 0) {
            include '../sys/processpayment.php';
            DBcommand::ExecuteQuery($db, 'exec PAYMENT_updatepnrp @pnrpid=?,@pnrpresponsestatus=?', [$pnrid, "Completed"]);
          }
            
          if (empty($data)) {
              echo json_encode(['status' => 'error', 'message' =>  $errorText, $responseData["status_error_code"]]);
          }
          echo json_encode(['status' => 'success', 'message' => 'Payment successful']);
        }
    } else {
        $errorText = mapErrorCodes(strval($response_data["status_error_code"]));
        handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
        echo json_encode(['status' => 'error', 'message' =>  $errorText, $response_data["status_error_code"]]);
    }
  } else {
    $errorText = mapErrorCodes(strval($response_data["status_error_code"]));
    handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
    echo json_encode(['status' => 'error', 'message' =>  $errorText, $response_data["status_error_code"]]);
  }
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}

//process

<?php

include_once "helpers.php";
include_once "../datadog/index.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        include_once "../sys/sessionsettings.php";
        $companyterminalid = 0;
	    $paymentfromcrs = 0;
        $request_data = json_decode(file_get_contents('php://input'), true);
        $requiredFields = [
            'sale_price',
            'product_name',
            'currency',
            'buyer_key',
            'seller_payme_id',
            'orderId',
            'pnrtotal',
            'terminalrtid',
            'bookingengineurl',
            'pnrunq',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($request_data[$field]) || empty($request_data[$field])) {
                echo json_encode(['status' => 'error', 'message' => 'Payment failed: Missing required fields. Check your details and try again.', 'redirecturl' => '']);
                die;
            }
        }

        $_GET['rtid'] = $request_data['rtid'];
        $rtid = $request_data['rtid'];

        $sale_price = $request_data['sale_price'];
        $product_name = $request_data['product_name'];
        $rtname = $request_data['currency'];
        $buyer_key = $request_data['buyer_key'];
        $seller_payme_id = $request_data['seller_payme_id'];
        $orderId = $request_data['orderId'];
        $pnrtotal = $request_data['pnrtotal'];
        $terminalrtid = $request_data['terminalrtid'];
        $bookingengineurl = $request_data['bookingengineurl'];
        $pnrunq = $request_data['pnrunq'];
        $lang = $request_data['lang'];
        

        include_once "../sys/getinfo.php";

        $ppurl = $bookingengineurl . '/booking/' . $pnrunq . '/error';
        $pnrpid = explode("-", $orderId)[1];
        $db = new DBsession('africainv');

        $url = $companyterminalvar4.'/api/generate-sale';

        $body = [
            'sale_callback_url' => $companyterminalurl.'/payme/listener.php',
            'sale_price' => $sale_price * 100,
            'product_name' => $product_name,
            'currency' => $rtname,
            'buyer_key' => $buyer_key,
            'seller_payme_id' => $seller_payme_id,
            'transaction_id' => $bookingref . '-' . $pnrpid
        ];
        
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $response = curl_exec($ch);

        logSenderDataDogGeneral([[
            "request" => $body, "response" => $response, "pnrunq" => $pnrunq, "pnrpid" => $pnrpid, "pnrid" => $pnrid
        ], 'payme', str_replace(" ", "-", strtolower($companyname)), ["process", "payme"]]);

        if (curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        $responseDataSale = json_decode($response, true);
        
        if ($responseDataSale['status_code'] === 0) {
            $paymentdata = $responseDataSale["payme_transaction_card_brand"] . "-" .substr($responseDataSale["buyer_card_mask"], -4);
            $paymentapproval = $responseDataSale['payme_sale_id'];
            $rcptCardHolderID = $responseDataSale['buyer_social_id'];
            $rcptCardHolder = $responseDataSale['buyer_name'];
            $res = DBcommand::ExecuteQuery(
                $db,
                'exec PAYMENT_updatepnrp @pnrpid=?,@pnrprtid=?,@pnrpstatus=?, @pnrpresmsg=?',
                [$pnrpid, $rtid, 'Completed', $responseDataSale['payme_sale_id']]
            );

            $pnrid = $pnrid;
            $companyterminalid = 0;
            $gatewayname = 'payme';
            $paymentdone = true;
            $rtname = $request_data['currency'];
            $RCPTPid = 0;
            $paymentamount = $responseDataSale['price'] / 100;

            $url = $companyterminalvar4.'/api/get-transactions';
            $body = [
                'seller_payme_id' => $seller_payme_id,
                'sale_payme_id' => $responseDataSale['payme_sale_id'],
            ];
            
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'cURL Error: ' . curl_error($ch);
                curl_close($ch);
                exit;
            }

            curl_close($ch);

            $responseDataTrx = json_decode($response, true);

            if (isset($responseDataTrx['items'][0])) {
                
                $item = $responseDataTrx['items'][0];
                if ($item['sale_status'] === 'completed') {
                    
                    if($paymentstatus == 0){
                        include_once "../sys/processpayment.php";
                    }
                    $ppurl = $bookingengineurl;
                    if (substr($bookingengineurl, -1) === '/') {
                        $ppurl .= 'confirmation/' . $pnrunq;
                    } else {
                        $ppurl .= '/confirmation/' . $pnrunq;
                    }
                    if (empty($data)) {
                        $errorText = mapErrorCodes(strval($responseDataTrx["status_error_code"]),$lang);
                        echo json_encode(['status' => 'error', 'message' =>  $errorText, $responseDataTrx["status_error_code"], 'redirecturl' => $ppurl]);
                    } else {
                        echo json_encode(['status' => 'success', 'message' => 'Payment successful', 'redirecturl' => $ppurl]);
                    }
                }
            } else {
                $errorText = mapErrorCodes(strval($responseDataTrx["status_error_code"]),$lang);
                handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
                echo json_encode(['status' => 'error', 'message' =>  $errorText, $responseDataTrx["status_error_code"], 'redirecturl' => $ppurl]);
            }
        }else{
            $errorText = mapErrorCodes(strval($responseDataSale["status_error_code"]),$lang);
            handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
            echo json_encode(['status' => 'error', 'message' =>  $errorText, 'redirecturl' => $ppurl]);
        }
    } catch (Exception $e) {
        $errorText = isset($errorText) ? $errorText : mapErrorCodes('999',$lang);
        handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
        echo json_encode(['status' => 'error', 'message' =>  $errorText, 'redirecturl' => $ppurl]);
    }
} else {
    $errorText = isset($errorText) ? $errorText : mapErrorCodes('998',$lang);
    handleFailure($db, $pnrpid, $pnrtotal, $rtid, $errorText);
    echo json_encode(['status' => 'error', 'message' =>  $errorText, 'redirecturl' => $ppurl]);
}


//css

@import url("https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap");

body{
    overflow-x: hidden;
    font-family: "Open Sans", sans-serif !important;

}
 
.container{
    font-family: "Open Sans", sans-serif !important;
    
}



.w {
  background-color: #007367;
}

.headerBottom {
    background-color: #007367 !important;
  }

.inner_headerBottom, .checkin_headerBottom {
    background-color: #007367 !important;
}
  
  .headerBottom_logo {
    height: unset !important;
    padding-top: 0 !important;
    padding: 10px !important;
  }
  
  .headerBottom_logo a img {
    height: 60px !important;
  }
  
  .centeredContent.paymentgateway img {
    min-width: 165px !important;
    min-height: 60px !important;
  }
  
  .centeredContent.paymentgateway {
    max-width: 820px !important;
    display: flex !important;
    direction: rtl !important;
    padding: 0 !important;
    width: unset !important;
    height: unset !important;
  }
  
  .progressBar_holder {
    display: none;
  }
  
  .control-label,
  input,
  .form-control {
    color: #1b192c;
    font-family: "Open Sans", sans-serif !important;
    font-size: 15px;
    font-weight: 400;
  }
  
  .help-block {
    font-family: 'Open Sans', sans-serif;
    font-weight: 400;
    font-size: 13px !important;
    color: #ff0033 !important;
  }
  
  #submit-button {
    font-family: 'Open Sans', sans-serif;
  }
  
  #submit-button:hover {
    color: #fff;
    background: rgb(4 30 67 / 92%) !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
  }


.headerBottom_logo {
  height: unset !important;
  padding-top: 0;
  padding: 10px;
}

.headerBottom_logo a img {
  height: 60px !important;
}

.centeredContent.paymentgateway img {
  min-width: 165px;
  min-height: 60px;
}

.centeredContent.paymentgateway {
  max-width: 820px;
  display: flex;
  direction: rtl;
  padding: 0;
  width: unset;
  height: unset;
}

.progressBar_holder {
  display: none;
}

.control-label,
input,
.form-control {
  color: #1b192c;
  font-family: "Open Sans", sans-serif !important;
  font-size: 15px;
  font-weight: 400;
}

.help-block {
  font-family: 'Open Sans', sans-serif;
  font-weight: 400;
  font-size: 13px !important;
  color: #ff0033 !important;
}

#submit-button {
  font-family: 'Open Sans', sans-serif;
}

#submit-button:hover {
  color: #fff;
  background: rgb(4 30 67 / 92%) !important;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
.row-submit{
    display: flex; 
    justify-content: center;
}
   .container {
    max-width: 1200px;

  }


  
  select option:hover {
    overflow: visible;
    white-space: normal;
    background-color: yellow; /* Optional: Change background for better readability */
  }

  #loader img {
        width: 60px;
        height: 60px;
        display: block;
        max-width: 100%;
        object-fit: contain;
        z-index: 1001;
        background-color: red; /* Temporary debug style */
      
      
      } */
      
      #loader-overlay {
        position: fixed;
            width: 100%;
            height: 100vh; /* Full viewport height */
            background-color: red;
          }
      
          #loader-overlay img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            z-index : 1000
          }
      
      
      
      

          * {
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-variant: normal;
            padding: 0;
            margin: 0;
        }
        
        html {
            height: 100%;
        }
        
        body {
            background: #F6F9FC;
            display: flex;
            min-height: 100%;
        }
        
        form {
            width: 600px;
            margin: 20px auto;
        }
        
        label {
            position: relative;
            color: #6A7C94;
            font-weight: 400;
            height: 48px;
            line-height: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        label>span {
            float: left;
        }
        
        .field {
            background: white;
            box-sizing: border-box;
            font-weight: 400;
            border: 1px solid #CFD7DF;
            border-radius: 24px;
            color: #32315E;
            outline: none;
            height: 48px;
            line-height: 48px;
            padding: 0 20px;
            cursor: text;
            float: left;
            width: 74%;
            margin-top: 6px;
            margin-left: -6px;
        }
        
        .field::-webkit-input-placeholder {
            color: #CFD7DF;
        }
        
        .field::-moz-placeholder {
            color: #CFD7DF;
        }
        
        .field:-ms-input-placeholder {
            color: #CFD7DF;
        }
        
        .field:focus,
        .field.StripeElement--focus {
            border-color: #F99A52;
        }
        
        button {
            float: left;
            display: block;
            color: white;
            border-radius: 24px;
            border: 0;
            margin-top: 20px !important;
            font-size: 17px;
            font-weight: 500;
            width: 74%;
            height: 48px;
            line-height: 48px;
            outline: none;
            margin-left: -6px;
        }
        
        
        .outcome {
            float: left;
            width: 100%;
            padding-top: 8px;
            min-height: 20px;
            text-align: center;
        }
        
        .success,
        .error {
            display: none;
            font-size: 13px;
        }
        
        .success.visible,
        .error.visible {
            display: inline;
        }
        
        .error {
            color: #E4584C;
        }
        
        .success {
            color: #F8B563;
        }
        
        .success .token {
            font-weight: 500;
            font-size: 13px;
        }
        
        footer {
            top: 96%;
            width: 100%;
        }
        
        section {
            width: 100%;
        }
        
        #saveCardDiv {
            float: left;
        }
        
        /* Absolute Center Spinner */
        .loading_zooz {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
            overflow: show;
            margin: auto;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        /* Transparent Overlay */
        .loading_zooz:before {
            content: '';
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(rgba(20, 20, 20, .8), rgba(0, 0, 0, .8));
        
            background: -webkit-radial-gradient(rgba(20, 20, 20, .8), rgba(0, 0, 0, .8));
        }
        
        /* :not(:required) hides these rules from IE9 and below */
        .loading_zooz:not(:required) {
            /* hide "loading..." text */
            font: 0/0 a;
            color: transparent;
            text-shadow: none;
            background-color: transparent;
            border: 0;
        }
        
        .loading_zooz:not(:required):after {
            content: '';
            display: block;
            font-size: 10px;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            -webkit-animation: spinner 150ms infinite linear;
            -moz-animation: spinner 150ms infinite linear;
            -ms-animation: spinner 150ms infinite linear;
            -o-animation: spinner 150ms infinite linear;
            animation: spinner 150ms infinite linear;
            border-radius: 0.5em;
            -webkit-box-shadow: rgba(255, 255, 255, 0.75) 1.5em 0 0 0, rgba(255, 255, 255, 0.75) 1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) 0 1.5em 0 0, rgba(255, 255, 255, 0.75) -1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) -1.5em 0 0 0, rgba(255, 255, 255, 0.75) -1.1em -1.1em 0 0, rgba(255, 255, 255, 0.75) 0 -1.5em 0 0, rgba(255, 255, 255, 0.75) 1.1em -1.1em 0 0;
            box-shadow: rgba(255, 255, 255, 0.75) 1.5em 0 0 0, rgba(255, 255, 255, 0.75) 1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) 0 1.5em 0 0, rgba(255, 255, 255, 0.75) -1.1em 1.1em 0 0, rgba(255, 255, 255, 0.75) -1.5em 0 0 0, rgba(255, 255, 255, 0.75) -1.1em -1.1em 0 0, rgba(255, 255, 255, 0.75) 0 -1.5em 0 0, rgba(255, 255, 255, 0.75) 1.1em -1.1em 0 0;
        }
        
        /* Animation */
        
        @-webkit-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
        
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        
        @-moz-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
        
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        
        @-o-keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
        
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        
        @keyframes spinner {
            0% {
                -webkit-transform: rotate(0deg);
                -moz-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }
        
            100% {
                -webkit-transform: rotate(360deg);
                -moz-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        
        body {
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-line-pack: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            flex-direction: column;
            min-height: 100vh;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            font-family: 'Roboto', sans-serif;
        }
        
        .payment-title {
            width: 100%;
            text-align: center;
        }
        
        .form-container .field-container:first-of-type {
            grid-area: name;
        }
        
        .form-container .field-container:nth-of-type(2) {
            grid-area: number;
        }
        
        .form-container .field-container:nth-of-type(3) {
            grid-area: expiration;
        }
        
        .form-container .field-container:nth-of-type(4) {
            grid-area: security;
        }
        
        .field-container input {
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }
        
        .field-container {
            position: relative;
        }
        
        .form-container {
            display: grid;
            grid-column-gap: 10px;
            grid-template-columns: auto auto;
            grid-template-rows: 90px 90px 90px;
            grid-template-areas:
                "name name"
                "number number"
                "expiration security";
            max-width: 400px;
            color: #707070;
            transition: all .5s ease;
            margin: 20px 0;
        }
        
        label {
            padding-bottom: 5px;
            font-size: 13px;
        }
        
        input {
            margin-top: 3px;
            padding: 15px;
            font-size: 16px;
            width: 100%;
            border-radius: 3px;
            border: 1px solid #dcdcdc;
        }
        
        .ccicon {
            height: 38px;
            position: absolute;
            right: 6px;
            top: calc(50% - 17px);
            width: 60px;
        }
        
        /* CREDIT CARD IMAGE STYLING */
        .preload * {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
        }
        
        .formcontainer {
            width: 100%;
            max-width: 400px;
            max-height: 251px;
            height: 54vw;
            transition: all .5s ease;
            margin: 20px 0;
        }
        
        #ccsingle {
            position: absolute;
            right: 15px;
            top: 20px;
        }
        
        #ccsingle svg {
            width: 100px;
            max-height: 60px;
        }
        
        .creditcard svg#cardfront,
        .creditcard svg#cardback {
            width: 100%;
            -webkit-box-shadow: 1px 5px 6px 0px black;
            box-shadow: 1px 5px 6px 0px black;
            border-radius: 22px;
        }
        

        
        /* CHANGEABLE CARD ELEMENTS */
        .creditcard .lightcolor,
        .creditcard .darkcolor {
            -webkit-transition: fill .5s;
            transition: fill .5s;
        }
        
        .creditcard .lightblue {
            fill: #03A9F4;
        }
        
        .creditcard .lightbluedark {
            fill: #0288D1;
        }
        
        .creditcard .red {
            fill: #ef5350;
        }
        
        .creditcard .reddark {
            fill: #d32f2f;
        }
        
        .creditcard .purple {
            fill: #ab47bc;
        }
        
        .creditcard .purpledark {
            fill: #7b1fa2;
        }
        
        .creditcard .cyan {
            fill: #26c6da;
        }
        
        .creditcard .cyandark {
            fill: #0097a7;
        }
        
        .creditcard .green {
            fill: #66bb6a;
        }
        
        .creditcard .greendark {
            fill: #388e3c;
        }
        
        .creditcard .lime {
            fill: #d4e157;
        }
        
        .creditcard .limedark {
            fill: #afb42b;
        }
        
        .creditcard .yellow {
            fill: #ffeb3b;
        }
        
        .creditcard .yellowdark {
            fill: #f9a825;
        }
        
        .creditcard .orange {
            fill: #ff9800;
        }
        
        .creditcard .orangedark {
            fill: #ef6c00;
        }
        
        .creditcard .grey {
            fill: #bdbdbd;
        }
        
        .creditcard .greydark {
            fill: #616161;
        }
        
        /* FRONT OF CARD */
        #svgname {
            text-transform: uppercase;
        }
        
        #cardfront .st2 {
            fill: #FFFFFF;
        }
        
        #cardfront .st3 {
            font-family: 'Source Code Pro', monospace;
            font-weight: 600;
        }
        
        #cardfront .st4 {
            font-size: 54.7817px;
        }
        
        #cardfront .st5 {
            font-family: 'Source Code Pro', monospace;
            font-weight: 400;
        }
        
        #cardfront .st6 {
            font-size: 33.1112px;
        }
        
        #cardfront .st7 {
            opacity: 0.6;
            fill: #FFFFFF;
        }
        
        #cardfront .st8 {
            font-size: 24px;
        }
        
        #cardfront .st9 {
            font-size: 36.5498px;
        }
        
        #cardfront .st10 {
            font-family: 'Source Code Pro', monospace;
            font-weight: 300;
        }
        
        #cardfront .st11 {
            font-size: 16.1716px;
        }
        
        #cardfront .st12 {
            fill: #4C4C4C;
        }
        
        /* BACK OF CARD */
        #cardback .st0 {
            fill: none;
            stroke: #0F0F0F;
            stroke-miterlimit: 10;
        }
        
        #cardback .st2 {
            fill: #111111;
        }
        
        #cardback .st3 {
            fill: #F2F2F2;
        }
        
        #cardback .st4 {
            fill: #D8D2DB;
        }
        
        #cardback .st5 {
            fill: #C4C4C4;
        }
        
        #cardback .st6 {
            font-family: 'Source Code Pro', monospace;
            font-weight: 400;
        }
        
        #cardback .st7 {
            font-size: 27px;
        }
        
        #cardback .st8 {
            opacity: 0.6;
        }
        
        #cardback .st9 {
            fill: #FFFFFF;
        }
        
        #cardback .st10 {
            font-size: 24px;
        }
        
        #cardback .st11 {
            fill: #EAEAEA;
        }
        
        #cardback .st12 {
            font-family: 'Rock Salt', cursive;
        }
        
        #cardback .st13 {
            font-size: 37.769px;
        }
        
        /* FLIP ANIMATION */
        .formcontainer {
            perspective: 1000px;
            /* margin: auto; */
            /* float: left; */
        }
        
        .creditcard {
            width: 100%;
            max-width: 400px;
            -webkit-transform-style: preserve-3d;
            transform-style: preserve-3d;
            transition: -webkit-transform 0.6s;
            -webkit-transition: -webkit-transform 0.6s;
            transition: transform 0.6s;
            transition: transform 0.6s, -webkit-transform 0.6s;
            cursor: pointer;
        }
        
        .creditcard .front,
        .creditcard .back {
            position: absolute;
            width: 100%;
            max-width: 400px;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-font-smoothing: antialiased;
            color: #47525d;
        }
        
        .creditcard .back {
            -webkit-transform: rotateY(180deg);
            transform: rotateY(180deg);
        }
        
        .creditcard.flipped {
            -webkit-transform: rotateY(180deg);
            transform: rotateY(180deg);
        }
        
        /* MAIN CREDIT CARD CONTAINER */
        

        
        .credit-card.selectable:hover {
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        }
        
        
        /*  NUMBER FORMATTING */
        
        .credit-card-last4 {
            font-family: "PT Mono", Helvetica, sans-serif;
            font-size: 24px;
            padding-bottom: 20px;
        }
        
        .credit-card-last4:before {
            content: "**** **** **** ";
            color: #4f4d4d;
            font-size: 20px;
        }
        
        .credit-card.american-express .credit-card-last4:before,
        .credit-card.amex .credit-card-last4:before {
            content: "**** ****** *";
            margin-right: -10px;
        }
        
        .credit-card.diners-club .credit-card-last4:before,
        .credit-card.diners .credit-card-last4:before {
            content: "**** ****** ";
        }
        
        .credit-card-expiry {
            font-family: "PT Mono", Helvetica, sans-serif;
            font-size: 18px;
            position: absolute;
            bottom: 5px;
            left: 15px;
        }
        
        
        /* BRAND CUSTOMIZATION */
        
        .credit-card.visa,
        .credit-card_back.visa {
            background: #4862e2;
            color: #eaeef2;
        }
        
        .credit-card.visa .credit-card-last4:before {
            color: #8999e5;
        }
        
        .credit-card.mastercard,
        .credit-card_back.mastercard {
            background: #4f0cd6;
            color: #e3e8ef;
        }
        
        .credit-card.mastercard .credit-card-last4:before {
            color: #8a82dd;
        }
        
        .credit-card.american-express,
        .credit-card.amex,
        .credit-card_back.amex {
            background: #1cd8b3;
            color: #f2fcfa;
        }
        
        .credit-card.american-express .credit-card-last4:before,
        .credit-card.amex .credit-card-last4:before {
            color: #99efe0;
        }
        
        .credit-card.diners,
        .credit-card.diners-club,
        .credit-card_back.diners {
            background: #8a38ff;
            color: #f5efff;
        }
        
        .credit-card.diners .credit-card-last4:before,
        .credit-card.diners-club .credit-card-last4:before {
            color: #b284f4;
        }
        
        .credit-card.discover,
        .credit-card_back.discover {
            background: #f16821;
            color: #fff4ef;
        }
        
        .credit-card.discover .credit-card-last4:before {
            color: #ffae84;
        }
        
        .credit-card.jcb,
        .credit-card_back.jcb {
            background: #cc3737;
            color: #f7e8e8;
        }
        
        .credit-card.jcb .credit-card-last4:before {
            color: #f28a8a;
        }
        
        .credit-card.unionpay,
        .credit-card_back {
            background: #47bfff;
            color: #fafdff;
        }
        
        .credit-card.unionpay .credit-card-last4:before {
            color: #99dcff;
        }
        
        
        /*   LOGOS  */
        
        .credit-card::after {
            content: " ";
            position: absolute;
            bottom: 10px;
            right: 15px;
        }
        
        .credit-card.visa::after {
            height: 16px;
            width: 50px;
            background-image: url('data:image/png;base64,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');
        }
        
        .credit-card.mastercard::after {
            width: 40px;
            height: 25px;
            background-image: url('data:image/png;base64,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');
        }
        
        .credit-card.amex::after,
        .credit-card.american-express::after {
            width: 50px;
            height: 14px;
            background-image: url('data:image/png;base64,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');
        }
        
        .credit-card.diners::after,
        .credit-card.diners-club::after {
            width: 30px;
            height: 24px;
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAYCAYAAADtaU2/AAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAAED2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iPgogICAgICAgICA8eG1wTU06RG9jdW1lbnRJRD54bXAuZGlkOkFDMEM4Rjk2NTQzRDExRTQ5MzZBQzlERDRCNDEwQzZDPC94bXBNTTpEb2N1bWVudElEPgogICAgICAgICA8eG1wTU06RGVyaXZlZEZyb20gcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICA8c3RSZWY6aW5zdGFuY2VJRD54bXAuaWlkOkFDMEM4RjkzNTQzRDExRTQ5MzZBQzlERDRCNDEwQzZDPC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOkFDMEM4Rjk0NTQzRDExRTQ5MzZBQzlERDRCNDEwQzZDPC9zdFJlZjpkb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9t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');
        }
        
        .credit-card.discover::after {
            width: 50px;
            height: 14px;
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAOCAYAAABth09nAAAAAXNSR0IArs4c6QAAAAlwSFlzAAALEwAACxMBAJqcGAAAAVlpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IlhNUCBDb3JlIDUuNC4wIj4KICAgPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICAgICAgPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iPgogICAgICAgICA8dGlmZjpPcmllbnRhdGlvbj4xPC90aWZmOk9yaWVudGF0aW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KTMInWQAAA/tJREFUSA19ll2IVVUUx73jmEYgiUpYiIREkFqQD0FhKiVIoPaSRaEPQlATZmk9JERBQVCSldjHk9iDqE8KQaTQB0lFoljDSGKiFI3VWJZZfo6n32+fta5nrrcW/O/6r8+999n77HNbVVWNHnVZqlardSlNYj1yffAWVFt9qSNPX/axxzB2kehhfERdjNs5njmO4RyyBlok52W80pNziLmVpPaPA8fgnT4btyWL0bmAdixJt1inr9FnRP/s8X+aWuda6lqQmZHsUxxklX9qR8J0Ob7vwzcDPQHsx/cPOb3oixG7A309OIjvELEetDt5Nb5ZwAV/h++kdXB7/Yp9HBvlVKqx+G4Bx4H8WnAejAH6zoCJwB05Rc0faOd6lT8X7BByHv0OuCYSDsA/D64/ZQgyO/yz4P0ZCH1fxJZiH2nEfoGvjNg+eH9wF+ZcHo/ce9EfBE+1FvJkGL+jz4G3wFTQZ7GyHcwFG4CyOxoPwHeB2TqRl8EU8CKYDCaCU+Bv8DBwUW+AuWAeSHFnRMqDkAfCmONYCvZBcDS4D/EwuAvcDyaBV4GyGDxXWFU9gx6fC3m+dKqbZcIcEvaAj4Bc2ZB5Mdibtbu6vemP2LcRO4POhZwN3zH0DeA02B75Tlh5KuxD8E/lKdivm9CwNd8ttgx5pRG8sXZVK9Efg6+Mod8Pv5NaD3zR9oIjEdf2XJtrD3dJGa5V+zd35mY87YnB346MSdHj67B/Cv0Q2uOlrAFbCquqheaXsylpyLjgvkiuvuTwYi2ncD32KuBTGwJeDNcBxdxyXaPz+tTfFHOEN42Lfg88Td8X0EvBVsY5gVYcdxC8BBxjL/AiUNbVatQ88j+jvieP1toIaG8CimfyC2DiOOCNVATuEfHlfxYod2ZMjT0aeMaVbkfLm8rbz1z7p9ytT8ExAD6srfoXeyOwtg8oSyK31wLlS+CknLjSFwlH4Z8Aj5LX7QrgApRHI+fn2iz1C+Bu+T1gfvi7qcfqqZUJPxIJe9IXfV2It9Nq8Bq4DawD7qjz3iZHbi11kH5wGHgWXciiEqiTd2BvAtPBNyBlM8Tvgw2nAS+EFHdrfsSWwX/IANpFPxExvyvWjwe/gXx46d+Jz8vgBFBWBdyRMWACGAQ77dOCWNj5VyE/Zp55eX70bsI+i/0j2kk0P4hTcHlcjhF397JHfhDtNUDsL7S10FY+3am4hrB9CMWP9lvm3IaB79Q5YA/frdMkeWn4oXTMk+Cy2ARccQHgK08pMyOvvNjGtDPW1Pi79ept5v8Xb/bpxqkr42fMiafDR1GeUAZzEP3BSy62T6ktzRjOK3YXX44x4s9mNqDeB2WsPT6+rGmnBSGt/mObOdr/Ap6tK4eqKaaFAAAAAElFTkSuQmCC');
        }
        
        .credit-card.jcb::after {
            width: 30px;
            height: 15px;
            background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAPCAYAAADzun+cAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAACXBIWXMAAAsTAAALEwEAmpwYAAABWWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS40LjAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyI+CiAgICAgICAgIDx0aWZmOk9yaWVudGF0aW9uPjE8L3RpZmY6T3JpZW50YXRpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgpMwidZAAACsklEQVQ4EX2U32uOYRjH32c2zIjyKyc7IDkRZw4cMEcjxIHSyoH4C6yNNJRJOZKWAyeEQqI4wQnlhE0phCHmR1NsI43ttdnm8fk8nktPWu+3Prvu676v57qv+3rve0kJpWk6FbMdVkICRemfS5LkiZPEbsSsgRpdUNpauEbc7WwiTVdjjXW+GDcd/4GJauAiVJJFGXusUhBrh/O4XYzHKsReryZwEzTBOFiZJ9ROgLIbgyRZgm11Ao1AsTPGzoAfxBl/CMxdzi3mX07jhl00YSjaZ1LHod8M6qEKxsB2TSbjFsG8fNFNJlPixkXFiT8y2Q1usAqmwC9QFvQUXoNdshhPth5cc3PVBf2gH9/OZtwIVX4UctP46AqXxIDN8BZMGK0/ztoK/Gb4Brb9IbyHOIixu4nbgu2BpXAH3+LaoM5AN1QmHwVPJ8obadstyPFnOADqLDSA2vHXlC5j4zDT8rlZ2DqYmftztG7sZbCtc2EhqPjIFsVls5gvVP2TC7SA8WJQFhWbZRP88TBx+nuM++A+qPkw4QdWdBc2wDN4BadBNcAy8ELZjeVsupbN/e3a4SvYbn/zIbC4uCfOq21wEHaC6oDUqgzYD1beAgOO2WAfdi/Es/F3U+dZO4J9B0fBFr6EPWCn3NgDbSXuObYTXsAbfA/n3fjuP4UWCA0z6IVyTOS2Ebvuvzld361vfEgHNUM9hO/cCBg3rpPrQvwOFJG10ncXby+eitVLyHm7IHFhohu2OjrEMHtudiE64Z3xTlWb0NYox7bbxCYqJvA2x+YRrzUuYFj6AM6bJxTrzsW3PSazAuVz0rcLVi76Z7hMj7BxOuNiPaw5bsFVKHatmMtYT/4YOlzoBRMPQpyKYVb1TewJHfQJboD/MIqyM51wigK9lGXGl6AWPG3IjbvhpK/iD/ZAl+AbzJMOAAAAAElFTkSuQmCC');
        }
        
        .credit-card.unionpay::after {
            width: 50px;
            height: 30px;
            background-image: url('data:image/png;base64,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');
        }
        
        
        
        /* The flip card container - set the width and height to whatever you want. We have added the border property to demonstrate that the flip itself goes out of the box on hover (remove perspective if you don't want the 3D effect */
        .flip-card {
            background-color: transparent;
            height: 200px;
            margin-bottom: -60px;
            perspective: 1000px;
            /* Remove this if you don't want the 3D effect */
        }
        
        /* This container is needed to position the front and back side */
        .flip-card-inner {
            margin: auto;
            position: relative;
            width: 70%;
            height: 50%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }
        
        /* Do an horizontal flip when you move the mouse over the flip box container */
        .flip-card:hover .flip-card-inner {
            transform: rotateY(180deg);
            -webkit-transform: rotateY(180deg);
            -moz-transform: rotateY(180deg);
            -o-transform: rotateY(180deg);
            -ms-transform: rotateY(180deg);
        }
        
        /* Position the front and back side */
        .flip-card-front,
        .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            /* Safari */
            backface-visibility: hidden;
            bottom: 0px;
        }
        
        
        /* Style the back side */
        .flip-card-back {
        
        
            width: 100%;
            max-width: 270px;
            transform: rotateY(-180deg);
            -webkit-transform: rotateY(-180deg);
            -moz-transform: rotateY(-180deg);
            -o-transform: rotateY(-180deg);
            -ms-transform: rotateY(-180deg);
        
        }
        
        .flip-card-delete{
            float:right; margin:5%; cursor:pointer;
        }
        
        .fieldSaved {
            background: white;
            box-sizing: border-box;
            font-weight: 400;
            border: 1px solid #CFD7DF;
            border-radius: 24px;
            color: #32315E;
            outline: none;
            height: 48px;
            font-size: 12px;
            line-height: 48px;
            padding: 0 20px;
            cursor: text;
            float: left;
            width: 30%;
            margin-top: 6px;
            margin-left: -6px;
        }
        
        .fieldSaved::-webkit-input-placeholder {
            color: #CFD7DF;
        }
        
        .fieldSaved::-moz-placeholder {
            color: #CFD7DF;
        }
        
        .fieldSaved:-ms-input-placeholder {
            color: #CFD7DF;
        }
        
        .fieldSaved:focus,
        .fieldSaved.StripeElement--focus {
            border-color: #F99A52;
        }
        
        .buttonSaved {
            margin: -48px 0px 0px 70px !important;
            float: left;
            display: block;
            color: white;
            border-radius: 24px;
            border: 0;
        
            font-size: 17px;
            font-weight: 500;
            width: 73%;
            height: 48px;
            line-height: 48px;
            outline: none;
            margin-left: -6px;
        }
        
        
        .backButton {
            margin: -48px 0px 0px 51px !important;
            float: left;
            display: block;
            color: white;
            border-radius: 24px;
            border: 0;
        
            font-size: 17px;
            font-weight: 500;
            width: 74%;
            height: 48px;
            line-height: 48px;
            outline: none;
            margin-left: -6px;
        }
        
        
        .backButtonDiv {
            margin-bottom: -175px;
        }
        
        .buttonPay {
            margin: 10px 0px 0px 3px !important;
            /* float: left; */
            display: block;
            /* background-image: linear-gradient(-180deg, #F8B563 0%, #F99A52 100%);
            
              box-shadow: 0 1px 2px 0 rgba(0,0,0,0.10), inset 0 -1px 0 0 #E57C45; */
            color: white;
            border-radius: 24px;
            border: 0;
            font-size: 17px;
            font-weight: 500;
            width: 100%;
            height: 48px;
            line-height: 48px;
            outline: none;
            margin-left: -6px;
            transition: all .5s ease;
        }
        
        
        .payBtnHolder {
            /* margin-top: 60px; */
            /* float: left; */
            /* top: 140px; */
            /* position: relative; */
            height: 100%;
            max-height: 270px;
            display: flex;
            flex-direction: column;
            margin-block-end: unset;
            align-items: center;
            justify-content: flex-end;
            /* transition: all .5s ease; */
            width: 25%;
        }
        
        .toggleSaved {
            margin: -48px 0px 0px 3px !important;
            float: left;
            display: block;
            color: white;
            border-radius: 24px;
            border: 0;
            font-size: 17px;
            font-weight: 500;
            width: 100%;
            height: 48px;
            line-height: 48px;
            outline: none;
            margin-left: -6px;
            transition: all .5s ease;
        }
        
        #formHolder label {
            display: inherit !important;
            line-height: inherit;
        }
        
        #formHolder input {
            margin-top: -32px;
        }
        
        .headerBottom {
            max-height: 115px;
        }
        
        #formHolder {
            /* margin: 50px 0; */
            width: 100%;
            max-width: 204vw;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
        }
        
        
        
        #formHolder:after {
            /* content: ""; */
            /* clear: both; */
            display: table;
        }
        
        .payBtnHolder div {
            margin: 0;
            margin-top: 8px !important;
            min-width: 200px;
            max-height: 270px;
        }
        

        
        @media screen and (max-width: 1200px) {
            .payBtnHolder {
                width: 100%;
                max-width: 400px;
                margin-left: auto;
                max-height: 150px;
            }
        }
        
        @media screen and (max-width:991px) {
            #formHolder {
                flex-direction: column;
            }
        
            .payBtnHolder {
                margin-left: unset;
            }
        }
        

        .control-label{
            color: #041e42
        }
        /* Base styles for the form */
.container {
    padding: 15px;
  }
  
.sbmt-btn{
    margin-bottom:30px;
     display:flex;
      justify-content: center;
      align-items: center;
      font-family: 'Open Sans', sans-serif !important;
}

.row-submit{
    /* width:100% */
}

.submit-con{
    width:100%
}

#submit-button{ 
     width:95% !important ;
     font-family: 'Open Sans', sans-serif !important;
 } 

  /* Make the form one column on mobile devices */
  @media (max-width: 767px) {
    .col-xs-7{
        width:100% !important;
  }
  
    .panel-body{
        /* width:50% !important; */
    }

    #checkout-form{
        width:98% !important;
    }

    .internal-inputs{
        font-family: 'Open Sans', sans-serif !important;
    }
    #card-number-group,
    #card-expiration-group,
    #card-cvv-group,
    #country-container,
    #first-name-group,
    #last-name-group,
    #email-group,
    #phone-group,
    #social-id-group,
    #zip-code-group {
      margin-bottom: 20px;
    }
    #card-number-group {
        margin-bottom: 20px;
    }
  
    .checkout-form{
        width:50%;
    }

    #submit-button{
        width:80% !important;
    } 

    .exp-cvv-con{
        width:100% !important;
        display:flex !important;
        justify-content: space-around !important;
    }

    .payment-errors{
        width:53%;
    }
 
  }

  
  @media (max-width: 400px) {
   
    .container{
        width: 101%;
        padding:7px !important;
    }
    .col-xs-7{
        width:100% !important;
  }
  
    #card-number-group,
    #card-expiration-group,
    #card-cvv-group,
    #country-container,
    #first-name-group,
    #last-name-group,
    #email-group,
    #phone-group,
    #social-id-group,
    #zip-code-group {
      margin-bottom: 15px;
    }
    #card-number-group {
        margin-bottom: 20px; /* Adds space between the rows */
    }
  
    .checkout-form{
        width:40%;
    }

    #submit-button{
        width:80% !important;
    } 

    .exp-cvv-con{
        width:100% !important;
        display:flex !important;
        justify-content: space-around !important;
    }

    .payment-errors{
        width:53%;
    }
}

  @media (max-width: 375px) {
    .container{
        width: 101%;
        padding:7px !important;
    }
    .col-xs-7{
        width:100% !important;
  }
  
    #card-number-group,
    #card-expiration-group,
    #card-cvv-group,
    #country-container,
    #first-name-group,
    #last-name-group,
    #email-group,
    #phone-group,
    #social-id-group,
    #zip-code-group {
      margin-bottom: 15px;
    }
    #card-number-group {
        margin-bottom: 20px; 
    }
  
    .checkout-form{
        width:40%;
    }

    #submit-button{
        width:80% !important;
    } 

    .exp-cvv-con{
        width:100% !important;
                display:flex !important;
        justify-content: space-around !important;
    }

    .payment-errors{
        width:53%;
    }
}




//helper:

<?php

const DETILES_NOT_VERIFIED       = ["en" =>"Your payment details could not be verified. Check your details and try again.", "he" =>"לא הצלחנו לאמת את פרטי הכרטיס. כדאי לבדוק שהם נכונים ולנסות שוב."];
const PAYMENT_NOT_PROCESSED      = ["en" =>"Payment could not be processed. Please try another payment method or contact your bank.", "he" =>"התשלום לא עבר. אפשר לנסות כרטיס אחר או ליצור קשר עם חברת האשראי."];  
const CONNECTION_PROCESSOR_ERROR = ["en" =>"There was a problem connecting to the payment processor. Please try again later.", "he" =>"נתקלנו בבעיית תקשורת. כדאי לנסות שוב מאוחר יותר"];

const FIELDS_MAP = [
    "card"=>["en"=>"card", "he"=>"כרטיס"],
    "cvv"=>["en"=>"cvv", "he"=>"CVV"],
    "exp"=>["en"=>"exp", "he"=>"תוקף"]
];

const ERRORS_MAP = [
    "attemts" => ["en"=>"We noticed multiple unsuccessful attempts to process your payment. For your security, we have temporarily paused further payment attempts. Thank you for your understanding and patience.","he"=>"שמנו לב לניסיונות רבים ולא מוצלחים לעבד את התשלום שלך. לשמירת הביטחון שלך, השהינו את הניסיונות לבצע תשלומים נוספים זמנית. תודה על ההבנה והסבלנות שלך."],
    "unexpected" => ["en"=>"An unexpected error occurred during payment processing.","he"=>"אירעה שגיאה בלתי צפויה בעת עיבוד התשלום שלך. נסה שוב מאוחר יותר או צור קשר עם הבנק שלך."],

];

function handleFailure($db, $pnrpid, $pnrtotal, $rtid, $error = '')
{
    if (strlen($error) > 140) {
        $error = substr($error, 0, 140);
    }
    
    DBcommand::ExecuteQuery(
        $db,
        'exec PAYMENT_updatepnrp @pnrpid=?,@pnrpamount=?,@pnrprtid=?,@pnrpstatus=?,@pnrpresmsg=?',
        array($pnrpid, $pnrtotal, $rtid, 'rejected', $error)
    );
}

function mapErrorCodes($code,$lang ='en')
{

    $map = [
        1 => PAYMENT_NOT_PROCESSED[$lang],
        10 => PAYMENT_NOT_PROCESSED[$lang],
        160 => PAYMENT_NOT_PROCESSED[$lang],
        165 => PAYMENT_NOT_PROCESSED[$lang],
        166 => PAYMENT_NOT_PROCESSED[$lang],
        171 => PAYMENT_NOT_PROCESSED[$lang],
        174 => PAYMENT_NOT_PROCESSED[$lang],
        180 => PAYMENT_NOT_PROCESSED[$lang],
        200 => PAYMENT_NOT_PROCESSED[$lang],
        20004 => PAYMENT_NOT_PROCESSED[$lang],
        20005 => PAYMENT_NOT_PROCESSED[$lang],
        20006 => PAYMENT_NOT_PROCESSED[$lang],
        20007 => PAYMENT_NOT_PROCESSED[$lang],
        20008 => PAYMENT_NOT_PROCESSED[$lang],
        20009 => PAYMENT_NOT_PROCESSED[$lang],
        20010 => PAYMENT_NOT_PROCESSED[$lang],
        20011 => PAYMENT_NOT_PROCESSED[$lang],
        20012 => PAYMENT_NOT_PROCESSED[$lang],
        20013 => PAYMENT_NOT_PROCESSED[$lang],
        20014 => PAYMENT_NOT_PROCESSED[$lang],
        20015 => PAYMENT_NOT_PROCESSED[$lang],
        20016 => PAYMENT_NOT_PROCESSED[$lang],
        201 => PAYMENT_NOT_PROCESSED[$lang],
        202 => PAYMENT_NOT_PROCESSED[$lang],
        21001 => PAYMENT_NOT_PROCESSED[$lang],
        21002 => PAYMENT_NOT_PROCESSED[$lang],
        21003 => PAYMENT_NOT_PROCESSED[$lang],
        21006 => PAYMENT_NOT_PROCESSED[$lang],
        21012 => PAYMENT_NOT_PROCESSED[$lang],
        21013 => PAYMENT_NOT_PROCESSED[$lang],
        21014 => PAYMENT_NOT_PROCESSED[$lang],
        21015 => PAYMENT_NOT_PROCESSED[$lang],
        21016 => PAYMENT_NOT_PROCESSED[$lang],
        21017 => PAYMENT_NOT_PROCESSED[$lang],
        21018 => PAYMENT_NOT_PROCESSED[$lang],
        22004 => PAYMENT_NOT_PROCESSED[$lang],
        22005 => PAYMENT_NOT_PROCESSED[$lang],
        23001 => PAYMENT_NOT_PROCESSED[$lang],
        23002 => PAYMENT_NOT_PROCESSED[$lang],
        23003 => PAYMENT_NOT_PROCESSED[$lang],
        250 => PAYMENT_NOT_PROCESSED[$lang],
        251 => PAYMENT_NOT_PROCESSED[$lang],
        252 => PAYMENT_NOT_PROCESSED[$lang],
        253 => PAYMENT_NOT_PROCESSED[$lang],
        254 => PAYMENT_NOT_PROCESSED[$lang],
        255 => PAYMENT_NOT_PROCESSED[$lang],
        312 => PAYMENT_NOT_PROCESSED[$lang],
        313 => PAYMENT_NOT_PROCESSED[$lang],
        320 => PAYMENT_NOT_PROCESSED[$lang],
        321 => PAYMENT_NOT_PROCESSED[$lang],
        322 => PAYMENT_NOT_PROCESSED[$lang],
        350 => PAYMENT_NOT_PROCESSED[$lang],
        351 => PAYMENT_NOT_PROCESSED[$lang],
        352 => PAYMENT_NOT_PROCESSED[$lang],
        353 => PAYMENT_NOT_PROCESSED[$lang],
        357 => PAYMENT_NOT_PROCESSED[$lang],
        358 => PAYMENT_NOT_PROCESSED[$lang],
        359 => PAYMENT_NOT_PROCESSED[$lang],
        360 => PAYMENT_NOT_PROCESSED[$lang],
        361 => PAYMENT_NOT_PROCESSED[$lang],
        362 => PAYMENT_NOT_PROCESSED[$lang],
        363 => PAYMENT_NOT_PROCESSED[$lang],
        370 => PAYMENT_NOT_PROCESSED[$lang],
        371 => PAYMENT_NOT_PROCESSED[$lang],
        372 => PAYMENT_NOT_PROCESSED[$lang],
        373 => PAYMENT_NOT_PROCESSED[$lang],
        374 => PAYMENT_NOT_PROCESSED[$lang],
        375 => PAYMENT_NOT_PROCESSED[$lang],
        376 => PAYMENT_NOT_PROCESSED[$lang],
        401 => PAYMENT_NOT_PROCESSED[$lang],
        402 => PAYMENT_NOT_PROCESSED[$lang],
        403 => PAYMENT_NOT_PROCESSED[$lang],
        404 => PAYMENT_NOT_PROCESSED[$lang],
        405 => PAYMENT_NOT_PROCESSED[$lang],
        406 => PAYMENT_NOT_PROCESSED[$lang],
        407 => PAYMENT_NOT_PROCESSED[$lang],
        408 => PAYMENT_NOT_PROCESSED[$lang],
        410 => PAYMENT_NOT_PROCESSED[$lang],
        411 => PAYMENT_NOT_PROCESSED[$lang],
        412 => PAYMENT_NOT_PROCESSED[$lang],
        420 => PAYMENT_NOT_PROCESSED[$lang],
        421 => PAYMENT_NOT_PROCESSED[$lang],
        422 => PAYMENT_NOT_PROCESSED[$lang],
        423 => PAYMENT_NOT_PROCESSED[$lang],
        424 => PAYMENT_NOT_PROCESSED[$lang],
        425 => PAYMENT_NOT_PROCESSED[$lang],
        426 => PAYMENT_NOT_PROCESSED[$lang],
        427 => PAYMENT_NOT_PROCESSED[$lang],
        428 => PAYMENT_NOT_PROCESSED[$lang],
        429 => PAYMENT_NOT_PROCESSED[$lang],
        430 => PAYMENT_NOT_PROCESSED[$lang],
        431 => PAYMENT_NOT_PROCESSED[$lang],
        435 => PAYMENT_NOT_PROCESSED[$lang],
        450 => PAYMENT_NOT_PROCESSED[$lang],
        481 => PAYMENT_NOT_PROCESSED[$lang],
        482 => PAYMENT_NOT_PROCESSED[$lang],
        483 => PAYMENT_NOT_PROCESSED[$lang],
        484 => PAYMENT_NOT_PROCESSED[$lang],
        500 => PAYMENT_NOT_PROCESSED[$lang],
        501 => PAYMENT_NOT_PROCESSED[$lang],
        502 => PAYMENT_NOT_PROCESSED[$lang],
        503 => PAYMENT_NOT_PROCESSED[$lang],
        504 => PAYMENT_NOT_PROCESSED[$lang],
        505 => PAYMENT_NOT_PROCESSED[$lang],
        506 => PAYMENT_NOT_PROCESSED[$lang],
        507 => PAYMENT_NOT_PROCESSED[$lang],
        510 => PAYMENT_NOT_PROCESSED[$lang],
        511 => PAYMENT_NOT_PROCESSED[$lang],
        512 => PAYMENT_NOT_PROCESSED[$lang],
        513 => PAYMENT_NOT_PROCESSED[$lang],
        514 => PAYMENT_NOT_PROCESSED[$lang],
        515 => PAYMENT_NOT_PROCESSED[$lang],
        516 => PAYMENT_NOT_PROCESSED[$lang],
        517 => PAYMENT_NOT_PROCESSED[$lang],
        518 => PAYMENT_NOT_PROCESSED[$lang],
        519 => PAYMENT_NOT_PROCESSED[$lang],
        520 => PAYMENT_NOT_PROCESSED[$lang],
        530 => PAYMENT_NOT_PROCESSED[$lang],
        531 => PAYMENT_NOT_PROCESSED[$lang],
        532 => PAYMENT_NOT_PROCESSED[$lang],
        533 => PAYMENT_NOT_PROCESSED[$lang],
        534 => PAYMENT_NOT_PROCESSED[$lang],
        535 => PAYMENT_NOT_PROCESSED[$lang],
        536 => PAYMENT_NOT_PROCESSED[$lang],
        537 => PAYMENT_NOT_PROCESSED[$lang],
        560 => PAYMENT_NOT_PROCESSED[$lang],
        561 => PAYMENT_NOT_PROCESSED[$lang],
        600 => PAYMENT_NOT_PROCESSED[$lang],
        601 => PAYMENT_NOT_PROCESSED[$lang],
        602 => PAYMENT_NOT_PROCESSED[$lang],
        603 => PAYMENT_NOT_PROCESSED[$lang],
        604 => PAYMENT_NOT_PROCESSED[$lang],
        605 => PAYMENT_NOT_PROCESSED[$lang],
        606 => PAYMENT_NOT_PROCESSED[$lang],
        607 => PAYMENT_NOT_PROCESSED[$lang],
        608 => PAYMENT_NOT_PROCESSED[$lang],
        609 => PAYMENT_NOT_PROCESSED[$lang],
        620 => PAYMENT_NOT_PROCESSED[$lang],
        100 => CONNECTION_PROCESSOR_ERROR[$lang],
        11 => CONNECTION_PROCESSOR_ERROR[$lang],
        14 => CONNECTION_PROCESSOR_ERROR[$lang],
        15 => CONNECTION_PROCESSOR_ERROR[$lang],
        16 => CONNECTION_PROCESSOR_ERROR[$lang],
        163 => CONNECTION_PROCESSOR_ERROR[$lang],
        164 => CONNECTION_PROCESSOR_ERROR[$lang],
        17 => CONNECTION_PROCESSOR_ERROR[$lang],
        18 => CONNECTION_PROCESSOR_ERROR[$lang],
        2 => CONNECTION_PROCESSOR_ERROR[$lang],
        20001 => CONNECTION_PROCESSOR_ERROR[$lang],
        20002 => CONNECTION_PROCESSOR_ERROR[$lang],
        20003 => CONNECTION_PROCESSOR_ERROR[$lang],
        21 => CONNECTION_PROCESSOR_ERROR[$lang],
        22 => CONNECTION_PROCESSOR_ERROR[$lang],
        3 => CONNECTION_PROCESSOR_ERROR[$lang],
        300 => CONNECTION_PROCESSOR_ERROR[$lang],
        301 => CONNECTION_PROCESSOR_ERROR[$lang],
        302 => CONNECTION_PROCESSOR_ERROR[$lang],
        303 => CONNECTION_PROCESSOR_ERROR[$lang],
        304 => CONNECTION_PROCESSOR_ERROR[$lang],
        305 => CONNECTION_PROCESSOR_ERROR[$lang],
        306 => CONNECTION_PROCESSOR_ERROR[$lang],
        309 => CONNECTION_PROCESSOR_ERROR[$lang],
        31 => CONNECTION_PROCESSOR_ERROR[$lang],
        310 => CONNECTION_PROCESSOR_ERROR[$lang],
        311 => CONNECTION_PROCESSOR_ERROR[$lang],
        32 => CONNECTION_PROCESSOR_ERROR[$lang],
        33 => CONNECTION_PROCESSOR_ERROR[$lang],
        34 => CONNECTION_PROCESSOR_ERROR[$lang],
        35 => CONNECTION_PROCESSOR_ERROR[$lang],
        36 => CONNECTION_PROCESSOR_ERROR[$lang],
        39 => CONNECTION_PROCESSOR_ERROR[$lang],
        4 => CONNECTION_PROCESSOR_ERROR[$lang],
        40 => CONNECTION_PROCESSOR_ERROR[$lang],
        41 => CONNECTION_PROCESSOR_ERROR[$lang],
        5 => CONNECTION_PROCESSOR_ERROR[$lang],
        640 => CONNECTION_PROCESSOR_ERROR[$lang],
        660 => CONNECTION_PROCESSOR_ERROR[$lang],
        680 => CONNECTION_PROCESSOR_ERROR[$lang],
        700 => CONNECTION_PROCESSOR_ERROR[$lang],
        701 => CONNECTION_PROCESSOR_ERROR[$lang],
        702 => CONNECTION_PROCESSOR_ERROR[$lang],
        703 => CONNECTION_PROCESSOR_ERROR[$lang],
        710 => CONNECTION_PROCESSOR_ERROR[$lang],
        711 => CONNECTION_PROCESSOR_ERROR[$lang],
        720 => CONNECTION_PROCESSOR_ERROR[$lang],
        721 => CONNECTION_PROCESSOR_ERROR[$lang],
        722 => CONNECTION_PROCESSOR_ERROR[$lang],
        724 => CONNECTION_PROCESSOR_ERROR[$lang],
        725 => CONNECTION_PROCESSOR_ERROR[$lang],
        726 => CONNECTION_PROCESSOR_ERROR[$lang],
        750 => CONNECTION_PROCESSOR_ERROR[$lang],
        751 => CONNECTION_PROCESSOR_ERROR[$lang],
        752 => CONNECTION_PROCESSOR_ERROR[$lang],
        753 => CONNECTION_PROCESSOR_ERROR[$lang],
        754 => CONNECTION_PROCESSOR_ERROR[$lang],
        755 => CONNECTION_PROCESSOR_ERROR[$lang],
        756 => CONNECTION_PROCESSOR_ERROR[$lang],
        757 => CONNECTION_PROCESSOR_ERROR[$lang],
        758 => CONNECTION_PROCESSOR_ERROR[$lang],
        790 => CONNECTION_PROCESSOR_ERROR[$lang],
        800 => CONNECTION_PROCESSOR_ERROR[$lang],
        801 => CONNECTION_PROCESSOR_ERROR[$lang],
        900 => CONNECTION_PROCESSOR_ERROR[$lang],
        901 => CONNECTION_PROCESSOR_ERROR[$lang],
        902 => CONNECTION_PROCESSOR_ERROR[$lang],
        903 => CONNECTION_PROCESSOR_ERROR[$lang],
        904 => CONNECTION_PROCESSOR_ERROR[$lang],
        905 => CONNECTION_PROCESSOR_ERROR[$lang],
        906 => CONNECTION_PROCESSOR_ERROR[$lang],
        907 => CONNECTION_PROCESSOR_ERROR[$lang],
        908 => CONNECTION_PROCESSOR_ERROR[$lang],
        909 => CONNECTION_PROCESSOR_ERROR[$lang],
        910 => CONNECTION_PROCESSOR_ERROR[$lang],
        911 => CONNECTION_PROCESSOR_ERROR[$lang],
        912 => CONNECTION_PROCESSOR_ERROR[$lang],
        913 => CONNECTION_PROCESSOR_ERROR[$lang],
        915 => CONNECTION_PROCESSOR_ERROR[$lang],
        916 => CONNECTION_PROCESSOR_ERROR[$lang],
        920 => CONNECTION_PROCESSOR_ERROR[$lang],
        921 => CONNECTION_PROCESSOR_ERROR[$lang],
        980 => CONNECTION_PROCESSOR_ERROR[$lang],
        981 => CONNECTION_PROCESSOR_ERROR[$lang],
        982 => CONNECTION_PROCESSOR_ERROR[$lang],
        983 => CONNECTION_PROCESSOR_ERROR[$lang],
        990 => CONNECTION_PROCESSOR_ERROR[$lang],
        991 => CONNECTION_PROCESSOR_ERROR[$lang],
        996 => CONNECTION_PROCESSOR_ERROR[$lang],
        997 => CONNECTION_PROCESSOR_ERROR[$lang],
        998 => CONNECTION_PROCESSOR_ERROR[$lang],
        999 => CONNECTION_PROCESSOR_ERROR[$lang],
        101 => DETILES_NOT_VERIFIED[$lang],
        103 => DETILES_NOT_VERIFIED[$lang],
        106 => DETILES_NOT_VERIFIED[$lang],
        107 => DETILES_NOT_VERIFIED[$lang],
        108 => DETILES_NOT_VERIFIED[$lang],
        109 => DETILES_NOT_VERIFIED[$lang],
        110 => DETILES_NOT_VERIFIED[$lang],
        111 => DETILES_NOT_VERIFIED[$lang],
        112 => DETILES_NOT_VERIFIED[$lang],
        114 => DETILES_NOT_VERIFIED[$lang],
        116 => DETILES_NOT_VERIFIED[$lang],
        117 => DETILES_NOT_VERIFIED[$lang],
        118 => DETILES_NOT_VERIFIED[$lang],
        119 => DETILES_NOT_VERIFIED[$lang],
        12 => DETILES_NOT_VERIFIED[$lang],
        120 => DETILES_NOT_VERIFIED[$lang],
        121 => DETILES_NOT_VERIFIED[$lang],
        13 => DETILES_NOT_VERIFIED[$lang],
        130 => DETILES_NOT_VERIFIED[$lang],
        131 => DETILES_NOT_VERIFIED[$lang],
        150 => DETILES_NOT_VERIFIED[$lang],
        151 => DETILES_NOT_VERIFIED[$lang],
        161 => DETILES_NOT_VERIFIED[$lang],
        162 => DETILES_NOT_VERIFIED[$lang],
        19 => DETILES_NOT_VERIFIED[$lang],
        20 => DETILES_NOT_VERIFIED[$lang],
        21004 => DETILES_NOT_VERIFIED[$lang],
        21005 => DETILES_NOT_VERIFIED[$lang],
        21007 => DETILES_NOT_VERIFIED[$lang],
        21008 => DETILES_NOT_VERIFIED[$lang],
        21009 => DETILES_NOT_VERIFIED[$lang],
        21010 => DETILES_NOT_VERIFIED[$lang],
        21011 => DETILES_NOT_VERIFIED[$lang],
        21019 => DETILES_NOT_VERIFIED[$lang],
        23 => DETILES_NOT_VERIFIED[$lang],
        24 => DETILES_NOT_VERIFIED[$lang],
        25 => DETILES_NOT_VERIFIED[$lang],
        26 => DETILES_NOT_VERIFIED[$lang],
        27 => DETILES_NOT_VERIFIED[$lang],
        28 => DETILES_NOT_VERIFIED[$lang],
        29 => DETILES_NOT_VERIFIED[$lang],
        30 => DETILES_NOT_VERIFIED[$lang],
        354 => DETILES_NOT_VERIFIED[$lang],
        355 => DETILES_NOT_VERIFIED[$lang],
        356 => DETILES_NOT_VERIFIED[$lang],
        37 => DETILES_NOT_VERIFIED[$lang],
        38 => DETILES_NOT_VERIFIED[$lang],
        42 => DETILES_NOT_VERIFIED[$lang],
        50 => DETILES_NOT_VERIFIED[$lang],
        723 => DETILES_NOT_VERIFIED[$lang]
    ];
    if (isset($map[$code])) {
        return $map[$code] . " " .  $code;
    } else {
        return PAYMENT_NOT_PROCESSED[$lang] . " " . $code;
    }
}