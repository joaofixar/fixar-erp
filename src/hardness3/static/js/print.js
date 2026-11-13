async function printSMT(pdfFile, nomeImpressora = '') {
  await authenticate();
  
  await connect({ retries: 2, delay: 1 });

  await launchQZ();

  await print(pdfFile, nomeImpressora);
}

async function print(pdfFile, nomeImpressora) {
  let impressora = await getPrinter(nomeImpressora);

  var config = createPrintConfig(impressora, pdfFile);

  var data = [{ 
        type:   'pixel',
        format: 'pdf',
        flavor: 'file',
        data:   pdfFile
  }];
  qz.print(config, data).catch(
      function(e) {
        console.error(e);
        console.warn('Falha ao realizar a impressão direto na impressora "' + impressora + '". Abrindo a impressão em uma nova janela.');
        window.open(pdfFile, '_blank');
      }
    );
}

function createPrintConfig(impressora, pdfFile) {
  if (isDanfceReceipt(pdfFile)) {
    return qz.configs.create(impressora, {
      units: 'mm',
      size: {
        width: 70
      },
      margins: 0,
      scaleContent: true,
      rasterize: true,
      orientation: 'portrait'
    });
  }

  return qz.configs.create(impressora);
}

function isDanfceReceipt(pdfFile) {
  return typeof pdfFile === 'string' && pdfFile.indexOf('impressaoDANFCe=true') !== -1;
}

async function getPrinter(nomeImpressora) {
  if (!nomeImpressora) {
    return await findDefaultPrinter();
  } else {
    return nomeImpressora;
  }
}

async function findDefaultPrinter() {
  return await qz.printers.getDefault();
}

async function launchQZ() {
  if (!qz.websocket.isActive()) {
    window.location.assign("qz:launch");
    await connect({ retries: 2, delay: 1 });
  }
}

async function connect(config) {
  config.host        = 'localhost';
  config.usingSecure = false;

  if (!qz.websocket.isActive()) {
      await qz.websocket.connect(config).then(function() {
        return true;
      }).catch(function(e) { console.error(e); });
  } else {
      return true;
  }
}

async function authenticate() {
  await qz.security.setCertificatePromise(
    function(resolve, reject) {
      resolve("-----BEGIN CERTIFICATE-----\n" +
              "MIIFOzCCBCOgAwIBAgISA86FuH+ToA50eECNIE8uBNj0MA0GCSqGSIb3DQEBCwUA\n" +
              "MDIxCzAJBgNVBAYTAlVTMRYwFAYDVQQKEw1MZXQncyBFbmNyeXB0MQswCQYDVQQD\n" +
              "EwJSMzAeFw0yMjAzMDgyMDU1MzZaFw0yMjA2MDYyMDU1MzVaMBsxGTAXBgNVBAMT\n" +
              "EHNtYXJ0bmVzcy5jb20uYnIwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\n" +
              "AQCrO/e3gEs8VRRILJgTsks+Vzfdr4wrY01LPxmuIowFlTGHkGFVaMQeyCcx1Nbp\n" +
              "yyr2VdsjkI0zAFjDQRVbvwMd3yM7/TNxkWTxGet290zpQQiUwud0Ax8qTpRPJbRA\n" +
              "ycn55WnXoZRX3l/D+bP3riIHTFCQ0qtpHglv2MxwyUfrAEDuf9mEcfY3mY9KvDnA\n" +
              "us1Aut43Tq3caBrQ+a2lrsVzfgu+wZiftseRUIw20SQ9FF0Btj4rFYA43hDDC++2\n" +
              "0XHDhHTq1uynWQT8zKjt3viher6GZXnh3ERgpHu4GaeTKGPLy4IJDAFwBVjTVrml\n" +
              "tPF2mhRE4vCPJvziFWzNkT17AgMBAAGjggJgMIICXDAOBgNVHQ8BAf8EBAMCBaAw\n" +
              "HQYDVR0lBBYwFAYIKwYBBQUHAwEGCCsGAQUFBwMCMAwGA1UdEwEB/wQCMAAwHQYD\n" +
              "VR0OBBYEFHIc5gGQhgwTqiKRA1kg3tb33o15MB8GA1UdIwQYMBaAFBQusxe3WFbL\n" +
              "rlAJQOYfr52LFMLGMFUGCCsGAQUFBwEBBEkwRzAhBggrBgEFBQcwAYYVaHR0cDov\n" +
              "L3IzLm8ubGVuY3Iub3JnMCIGCCsGAQUFBzAChhZodHRwOi8vcjMuaS5sZW5jci5v\n" +
              "cmcvMDEGA1UdEQQqMCiCEHNtYXJ0bmVzcy5jb20uYnKCFHd3dy5zbWFydG5lc3Mu\n" +
              "Y29tLmJyMEwGA1UdIARFMEMwCAYGZ4EMAQIBMDcGCysGAQQBgt8TAQEBMCgwJgYI\n" +
              "KwYBBQUHAgEWGmh0dHA6Ly9jcHMubGV0c2VuY3J5cHQub3JnMIIBAwYKKwYBBAHW\n" +
              "eQIEAgSB9ASB8QDvAHUAKXm+8J45OSHwVnOfY6V35b5XfZxgCvj5TV0mXCVdx4QA\n" +
              "AAF/a4fPNgAABAMARjBEAiArEZrvwSd9aCkj3LDPEbjArkW8yN2RMpkrnrqKITsD\n" +
              "VAIgIxZGKdQNdWfyD9fDvvRd+5hHm8PyaqxHFLaABj5mYNMAdgBByMqx3yJGShDG\n" +
              "oToJQodeTjGLGwPr60vHaPCQYpYG9gAAAX9rh9E+AAAEAwBHMEUCIQCWIOwuaV99\n" +
              "sloM2VJv+wT4gSq9cKftc3F5O1guORoI4AIgEG0Un7TvlcVEn0WkTkLebUMRjRV/\n" +
              "c44mpjYhcpyMzQUwDQYJKoZIhvcNAQELBQADggEBAAcvvaYx6LFKSSQ9yBlma3yj\n" +
              "CjKcvTBXARgbY8vW07ncke9SjuHaCVuA4Vj8iRAo0TqE/y9aMycbaYhAQ3rChMpA\n" +
              "HCd2ryOcJRYQZR50kzwJ122Zhx1t+ocEMpdna83E99IrwEnP+Bf3ubNBk9FypPqE\n" +
              "DGVCMc2kgIBG8akIoxXw/oKIZG4Hsto02NsHvvDDEw+AXMir/+vBEVZatnSFnN41\n" +
              "C9NyXAebHKOM/FKF7LhA72eWbTDL9buAxAv/wSaOw217Ss9WKFK2YfGFumv4dKUA\n" +
              "+PFlKmLvi9vSx1noUHcCwsjSCULax9i24y6AVTDqzUUFGDJdnoOFfCIgCAo89xY=\n" +
              "-----END CERTIFICATE-----\n");
      }
    );

  await qz.security.setSignatureAlgorithm("SHA512");
  await qz.security.setSignaturePromise(function(toSign) {
      return function(resolve, reject) {
          resolve();
      };
  });
}
