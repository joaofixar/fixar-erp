<?php

declare(strict_types=1);

namespace RestApi\Controller;

use RestApi\Core\Request;
use RestApi\Core\Response;
use RuntimeException;

class DocsController
{
    private string $rootPath;

    /** @var array<string, mixed> */
    private array $appConfig;

    /**
     * @param array<string, mixed> $appConfig
     */
    public function __construct(string $rootPath, array $appConfig)
    {
        $this->rootPath = rtrim($rootPath, '/');
        $this->appConfig = $appConfig;
    }

    /**
     * @return array{status:int,payload:string,headers:array<string,string>,raw:bool}
     */
    public function swaggerUi(Request $request): array
    {
        $docsPath = (string) (($this->appConfig['docs']['openapi_path'] ?? '/api/docs/openapi.yaml'));
        $title = (string) (($this->appConfig['app_name'] ?? 'REST API') . ' Docs');
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeDocsPath = json_encode($docsPath, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$safeTitle}</title>
  <meta name="color-scheme" content="light">
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
  <style>
    html, body {
      background: #ffffff;
      color: #111827;
      color-scheme: light;
    }

    #swagger-ui,
    .swagger-ui {
      background: #ffffff;
      color: #111827;
    }

    .swagger-ui .topbar,
    .swagger-ui .scheme-container,
    .swagger-ui .information-container,
    .swagger-ui .opblock-tag,
    .swagger-ui .opblock,
    .swagger-ui .responses-wrapper,
    .swagger-ui .model-box,
    .swagger-ui .parameter__type,
    .swagger-ui .parameter__name,
    .swagger-ui .response-col_status,
    .swagger-ui .response-col_description {
      color: #111827;
    }

    .swagger-write-disabled-note {
      margin-top: 8px;
      padding: 8px 10px;
      border-radius: 4px;
      background: #fff7ed;
      border: 1px solid #fdba74;
      color: #9a3412;
      font-size: 12px;
      line-height: 1.4;
    }
  </style>
</head>
<body>
  <div id="swagger-ui"></div>

  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
  <script>
    function clearSwaggerThemePreference() {
      var stores = [window.localStorage, window.sessionStorage];
      for (var storeIndex = 0; storeIndex < stores.length; storeIndex += 1) {
        var store = stores[storeIndex];
        if (!store) {
          continue;
        }

        var keysToRemove = [];
        for (var index = 0; index < store.length; index += 1) {
          var key = store.key(index);
          if (!key) {
            continue;
          }

          if (/swagger/i.test(key) && /(theme|mode|color)/i.test(key)) {
            keysToRemove.push(key);
          }
        }

        for (var removeIndex = 0; removeIndex < keysToRemove.length; removeIndex += 1) {
          try {
            store.removeItem(keysToRemove[removeIndex]);
          } catch (error) {
          }
        }
      }
    }

    function setSwaggerLightThemeAttributes() {
      var targets = [
        document.documentElement,
        document.body,
        document.getElementById("swagger-ui"),
        document.querySelector(".swagger-ui")
      ];

      for (var index = 0; index < targets.length; index += 1) {
        var target = targets[index];
        if (!target) {
          continue;
        }

        try {
          target.setAttribute("data-theme", "light");
          target.setAttribute("data-color-mode", "light");
          target.classList.remove("dark");
          target.classList.add("light");
          target.style.colorScheme = "light";
          target.style.backgroundColor = "#ffffff";
          target.style.color = "#111827";
        } catch (error) {
        }
      }
    }

    function isSwaggerDarkMode() {
      var target = document.querySelector(".swagger-ui") || document.getElementById("swagger-ui") || document.body;
      if (!target || !window.getComputedStyle) {
        return false;
      }

      var background = window.getComputedStyle(target).backgroundColor || "";
      var match = background.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/i);
      if (!match) {
        return false;
      }

      var red = parseInt(match[1], 10) || 0;
      var green = parseInt(match[2], 10) || 0;
      var blue = parseInt(match[3], 10) || 0;
      var brightness = (red * 299 + green * 587 + blue * 114) / 1000;

      return brightness < 140;
    }

    function findSwaggerThemeToggle() {
      var topbar = document.querySelector(".swagger-ui .topbar");
      if (!topbar) {
        return null;
      }

      var buttons = topbar.querySelectorAll("button");
      if (!buttons || buttons.length === 0) {
        return null;
      }

      for (var index = buttons.length - 1; index >= 0; index -= 1) {
        var button = buttons[index];
        var text = (button.textContent || "").replace(/\s+/g, " ").trim().toLowerCase();
        var hasSvg = button.querySelector("svg") !== null;

        if (text.indexOf("theme") !== -1 || text.indexOf("modo") !== -1 || text.indexOf("claro") !== -1 || text.indexOf("escuro") !== -1) {
          return button;
        }

        if (hasSvg && text === "") {
          return button;
        }
      }

      return buttons[buttons.length - 1];
    }

    function forceSwaggerLightMode(attempt) {
      var maxAttempts = 10;
      if (attempt > maxAttempts) {
        return;
      }

      setSwaggerLightThemeAttributes();

      if (!isSwaggerDarkMode()) {
        return;
      }

      var toggle = findSwaggerThemeToggle();
      if (toggle && typeof toggle.click === "function") {
        try {
          toggle.click();
        } catch (error) {
        }
      }

      window.setTimeout(function () {
        forceSwaggerLightMode(attempt + 1);
      }, 350);
    }

    function normalizeSwaggerPath(pathText) {
      return String(pathText || "").replace(/^https?:\/\/[^/]+/i, "").trim();
    }

    function canExecuteSwaggerOperation(path, method) {
      var normalizedMethod = String(method || "").toLowerCase();
      var normalizedPath = normalizeSwaggerPath(path);

      if (normalizedMethod === "get") {
        return true;
      }

      if (normalizedMethod === "post" && /\/validate$/i.test(normalizedPath)) {
        return true;
      }

      return false;
    }

    function applySwaggerExecutionRules() {
      var blocks = document.querySelectorAll(".swagger-ui .opblock");
      for (var index = 0; index < blocks.length; index += 1) {
        var block = blocks[index];
        var methodElement = block.querySelector(".opblock-summary-method");
        var pathElement = block.querySelector(".opblock-summary-path");
        if (!methodElement || !pathElement) {
          continue;
        }

        var method = (methodElement.textContent || "").trim();
        var path = (pathElement.textContent || "").trim();
        var executable = canExecuteSwaggerOperation(path, method);
        var tryOutButton = block.querySelector(".try-out__btn");
        var executeButton = block.querySelector(".execute");
        var note = block.querySelector(".swagger-write-disabled-note");

        if (executable) {
          if (tryOutButton) {
            tryOutButton.disabled = false;
            tryOutButton.style.display = "";
          }
          if (executeButton) {
            executeButton.disabled = false;
            executeButton.style.display = "";
          }
          if (note) {
            note.parentNode.removeChild(note);
          }
          continue;
        }

        if (tryOutButton) {
          tryOutButton.disabled = true;
          tryOutButton.style.display = "none";
        }
        if (executeButton) {
          executeButton.disabled = true;
          executeButton.style.display = "none";
        }

        if (!note) {
          note = document.createElement("div");
          note.className = "swagger-write-disabled-note";
          note.textContent = "Execução desabilitada no /api/docs. Este endpoint permanece documentado com exemplos, mas a escrita real deve ser testada via Postman ou integração controlada. Para validar payload sem gravar, use o endpoint /validate.";
          var wrapper = block.querySelector(".opblock-body") || block;
          wrapper.appendChild(note);
        }
      }
    }

    window.onload = function () {
      clearSwaggerThemePreference();
      setSwaggerLightThemeAttributes();

      window.ui = SwaggerUIBundle({
        url: {$safeDocsPath},
        dom_id: "#swagger-ui",
        deepLinking: true,
        defaultModelsExpandDepth: -1,
        docExpansion: "list",
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        layout: "StandaloneLayout",
        persistAuthorization: true
      });

      var applyRulesLater = function () {
        window.setTimeout(applySwaggerExecutionRules, 300);
      };

      applyRulesLater();
      window.setTimeout(function () {
        forceSwaggerLightMode(0);
      }, 200);

      if (typeof MutationObserver !== "undefined") {
        var observer = new MutationObserver(function () {
          applySwaggerExecutionRules();
          forceSwaggerLightMode(0);
        });

        observer.observe(document.getElementById("swagger-ui"), {
          childList: true,
          subtree: true
        });
      }
    };
  </script>
</body>
</html>
HTML;

        return Response::raw(
            $html,
            'text/html; charset=utf-8',
            200,
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    /**
     * @return array{status:int,payload:string,headers:array<string,string>,raw:bool}
     */
    public function openapi(Request $request): array
    {
        return Response::raw(
            $this->readDocsFile('openapi.yaml'),
            'application/yaml; charset=utf-8',
            200,
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    /**
     * @return array{status:int,payload:string,headers:array<string,string>,raw:bool}
     */
    public function postman(Request $request): array
    {
        return Response::raw(
            $this->readDocsFile('postman_collection.json'),
            'application/json; charset=utf-8',
            200,
            [
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    private function readDocsFile(string $filename): string
    {
        $file = $this->rootPath . '/docs/' . $filename;
        if (!file_exists($file)) {
            throw new RuntimeException('Documentation file not found.', 404);
        }

        $contents = file_get_contents($file);
        if ($contents === false) {
            throw new RuntimeException('Documentation file not found.', 404);
        }

        return $contents;
    }
}
