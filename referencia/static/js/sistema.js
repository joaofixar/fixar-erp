/**
 * sistema.js
 *
 * Versão legível de sistema-legado/static/js/sistema.js.
 * O original esconde cada texto num vetor e chama o texto por um número.
 * Aqui o número foi trocado pelo texto. Os nomes _0x… continuam,
 * porque o ofuscador não guardou o nome original.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */

$(document).ready(function () {
  device == "mobile" ? carregaMenuMobile() : carregaMenu();
  (setTimeout(function () {
    fixDesktopSize();
  }, 0x12c),
    $(window).bind("resize", function (_0x2b59ae) {
      if (_0x2b59ae.target !== window) return;
      (fixDesktopSize(),
        $(".ui-dialog").each(function () {
          const _0x197202 = $(this),
            _0x6b6afd = _0x197202.position(),
            _0x39d1e3 = _0x197202.find(".ui-dialog-content:first"),
            _0x239482 = $("#w-overlay-" + _0x39d1e3.data("janelaId"));
          if (_0x197202.hasClass("noBorder")) {
            (_0x197202.css({
              display: "flex",
              "flex-direction": "column",
              height:
                "calc(100% - " +
                desktopUp +
                "px - " +
                getExtraVerticalSpace(_0x197202) +
                "px)",
              width:
                "calc(100% - " +
                desktopLeft +
                "px - " +
                getExtraHorizontalSpace(_0x197202) +
                "px)",
              top: desktopUp,
              left: desktopLeft,
            }),
              _0x39d1e3.css({ flex: "1" }),
              _0x239482.css({
                height:
                  "calc(100% - " +
                  desktopUp +
                  "px - " +
                  getExtraVerticalSpace(_0x197202) +
                  "px)",
                width:
                  "calc(100% - " +
                  desktopLeft +
                  "px - " +
                  getExtraHorizontalSpace(_0x197202) +
                  "px)",
                top: _0x6b6afd.top,
                left: _0x6b6afd.left,
              }));
            return;
          }
          const _0x55f648 = _0x197202.hasClass("is-maximized");
          if (_0x55f648) {
            _0x239482.css({
              width: _0x197202.outerWidth() + "px",
              height: _0x197202.outerHeight() + "px",
              top: _0x6b6afd.top,
              left: _0x6b6afd.left,
            });
            return;
          }
          if (_0x39d1e3.data("originalHeight") == null) {
            const _0x1b9560 = parseFloat(
              getComputedStyle(_0x39d1e3[0x0])
                .getPropertyValue("height")
                .replace("px", ""),
            );
            _0x39d1e3.data("originalHeight", _0x1b9560);
          }
          if (_0x39d1e3.data("originalWidth") == null) {
            const _0x17bf95 = parseFloat(
              getComputedStyle(_0x39d1e3[0x0])
                .getPropertyValue("width")
                .replace("px", ""),
            );
            _0x39d1e3.data("originalWidth", _0x17bf95);
          }
          const _0x37b115 = _0x39d1e3.data("originalHeight"),
            _0x16546c = _0x39d1e3.data("originalWidth"),
            _0x4dc545 =
              window.innerHeight - this["getBoundingClientRect"]().top,
            _0x2d5cdc =
              window.innerWidth - this["getBoundingClientRect"]().left,
            _0x949fd5 = getFullHeight(_0x197202) - getFullHeight(_0x39d1e3);
          let _0x5d6ff2 = Math.min(
              _0x37b115,
              Math.max(
                0x0,
                _0x4dc545 - _0x949fd5 - getExtraVerticalSpace(_0x39d1e3),
              ),
            ),
            _0x43c112 = Math.min(
              _0x16546c,
              Math.max(0x0, _0x2d5cdc - getExtraHorizontalSpace(_0x39d1e3)),
            );
          (_0x197202.css("height", "auto"),
            _0x197202.width(_0x43c112),
            _0x39d1e3.width(_0x43c112),
            _0x39d1e3.height(_0x5d6ff2),
            _0x239482.css({
              width: _0x197202.outerWidth() + "px",
              height: _0x197202.outerHeight() + "px",
              top: _0x197202.position().top,
              left: _0x197202.position().left,
            }));
        }),
        $(".content:visible").each(function () {
          (corrigeConteudo($(this).attr("id")),
            corrigeGrid($(this).attr("id")));
        }));
    }));
  var _0x1e9435 = $("#toolbar");
  _0x1e9435.outerWidth(desktopWidth - (toolbarLeft + toolbarRight));
  if (!g.oneWindow) {
    (shortcut.add("Ctrl+Alt+Shift", function () {
      (!ctrlAltPressed &&
        $("<div>")
          .attr("id", "ctrlAltPressed")
          .css({
            width: "100%",
            position: "fixed",
            top: "200px",
            "text-align": "center",
            color: "white",
            "font-weight": "normal",
            "font-size": "20px",
            "z-index": "999999999",
            "background-color": "black",
            padding: "15px 0",
          })
          .html(
            '\n<span style="font-size: 1.3em; font-weight: bold;">I</span> (Informações da Janela) <span style="font-size: 1.3em; font-weight: bold;">E</span> (Ativar/Desativar ESC)<br />\n<span style="font-size: 1.3em; font-weight: bold;">C</span> (Contents) <span style="font-size: 1.3em; font-weight: bold;">T</span> (Tabs) <span style="font-size: 1.3em; font-weight: bold;">P</span> (Grid)<br />\n<span style="font-size: 1.3em; font-weight: bold;">F</span> (Form) <span style="font-size: 1.3em; font-weight: bold;">J</span> (Form Editor) <span style="font-size: 1.3em; font-weight: bold;">B</span> (Botões)<br/>\n<span style="font-size: 1.3em; font-weight: bold;">A</span> (Selecionar Tudo) <span style="font-size: 1.3em; font-weight: bold;">N</span> (Novo Campo)<br />\n<span style="font-size: 1.3em; font-weight: bold;">S</span> (SQL Grid) <span style="font-size: 1.3em; font-weight: bold;">L</span> (Limpar Filtros Grid)<br />\n<span style="font-size: 1.3em; font-weight: bold;">M</span> (Menu)<br />\n<span style="font-size: 1.3em; font-weight: bold;">G</span> (Conf. Global)<br />\n<span style="font-size: 1.3em; font-weight: bold;">R</span> (Restrições Tabelas)<br />\n<span style="font-size: 1.3em; font-weight: bold;">D</span> (Debug) <span style="font-size: 1.3em; font-weight: bold;">O</span> (Log) <span style="font-size: 1.3em; font-weight: bold;">U</span> (Lista Personalizações)<br />\n<span style="font-size: 1.3em; font-weight: bold;">V</span> (Modo Desenvolvedor)<br />\n<a href="#" style="color: white; font-weight: bold;" onclick="ctrlAltPressed = false; $(\'#ctrlAltPressed\').remove();">Fechar</a>\n',
          )
          .appendTo("body"),
        (ctrlAltPressed = true));
    }),
      shortcut.add("Ctrl+Alt+Shift+I", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0xc33731) {
            _0xc33731.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                diagramaPrimeraJanela($(".ui-dialog-content:visible:last")))
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+S", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0xd9f156) {
            _0xd9f156.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                dialogConfirm(
                  "Debug SQL",
                  $(".divToScroll:visible:last").attr("gridSqlComAlias"),
                ))
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+P", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x410649) {
              if (_0x410649.code) {
                ((ctrlAltPressed = false), $("#ctrlAltPressed").remove());
                var _0x232a49 = $(".content:visible:last");
                _0x232a49.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
                  (_0x232a49 = _0x232a49.find(".ui-tabs-panel:visible:last"));
                var _0x20176c = {
                  0: _0x232a49.data("href"),
                  1: _0x232a49.data("divIdRoot"),
                  2: _0x232a49.data("divId"),
                  3: _0x232a49.data("pesquisaGridId"),
                  4: _0x232a49.data("vars"),
                };
                gridPersonalizacao(_0x20176c, true);
              } else
                alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+L", function () {
        ((ctrlAltPressed = false), $("#ctrlAltPressed").remove());
        var _0x3ebcfc = $(".content:visible:last");
        return (
          _0x3ebcfc.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
            (_0x3ebcfc = _0x3ebcfc.find(".ui-tabs-panel:visible:last")),
          acoesGrid(_0x3ebcfc, true),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+C", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0xea90f) {
            _0xea90f.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                personalizacaoContents())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+T", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x5462c0) {
            _0x5462c0.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                personalizacaoTabs())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+B", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x551a72) {
            _0x551a72.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                personalizacaoBotoes())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+F", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x50f50c) {
            _0x50f50c.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                personalizacaoForms())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+J", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x4c95ac) {
            _0x4c95ac.code
              ? ($("#ctrlAltPressed").remove(),
                (ctrlAltPressed = false),
                showHideFormEditor())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+N", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x2c27e4) {
            _0x2c27e4.code
              ? ($("#ctrlAltPressed").remove(),
                (ctrlAltPressed = false),
                novoCampoFormEditor())
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+A", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x2374f9) {
              _0x2374f9.code
                ? ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  showHideFormEditor(true))
                : alert(
                    "Acesso negado! Somente o administrador tem acesso a esta opção.",
                  );
            },
          ),
          false
        );
      }));
    var _0x5b6be8 = getCookie("editarMenu");
    (shortcut.add("Ctrl+Alt+Shift+M", function () {
      return (
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x5a312d) {
            if (_0x5a312d.code) {
              ($("#ctrlAltPressed").remove(), (ctrlAltPressed = false));
              if (isempty(_0x5b6be8)) {
                var _0x1cad25 = "hardness";
                (setCookie("editarMenu", _0x1cad25, 0x0, true),
                  (window.location = "/erp"));
              } else
                (setCookie("editarMenu", "", 0x0, true),
                  (window.location = "/erp"));
            } else
              alert(
                "Acesso negado! Somente o administrador tem acesso a esta opção.",
              );
          },
        ),
        false
      );
    }),
      shortcut.add("Ctrl+Alt+Shift+D", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x3b0c39) {
              if (_0x3b0c39.code) {
                var _0x22c988 = getCookie("debug");
                ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  isempty(_0x22c988)
                    ? (setCookie("debug", unique(), 0x0, true),
                      dialogConfirm(
                        "Modo Debug!",
                        "O modo debug está ativo!<br />Utilize o sistema (faça alguma ação) e ao final precione <strong>CTRL + ALT + Shift + D</strong> para ver o resultado.",
                      ))
                    : (setCookie("debug", "", 0x0, true),
                      $.getJSON(
                        "/sistema/funcoes/informacoes/debug/?ajax=true&debug=" +
                          encodeURIComponent(_0x22c988) +
                          "&callback=?",
                        function (_0x17d0d9) {
                          (dialogConfirm(
                            "Relatório do Debug:",
                            '<div id="textareaDebug" style="overflow: auto; height: 500px; width: 800px;"></div>',
                          ),
                            setTimeout(function () {
                              $("#textareaDebug").html(_0x17d0d9.data);
                            }, 0xc8));
                        },
                      )));
              } else
                alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+O", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x1e737e) {
              if (_0x1e737e.code) {
                var _0x5c4e43 = getCookie("debuglog");
                ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  isempty(_0x5c4e43)
                    ? (setCookie("debuglog", unique(), 0x0, true),
                      dialogConfirm(
                        "Modo log!",
                        "O modo log está ativo!<br />Utilize o sistema (faça alguma ação) e ao final precione <strong>CTRL + ALT + Shift + O</strong> para ver o resultado.",
                      ))
                    : (setCookie("debuglog", "", 0x0, true),
                      $.getJSON(
                        "/sistema/funcoes/informacoes/debuglog/?ajax=true&debuglog=" +
                          encodeURIComponent(_0x5c4e43) +
                          "&callback=?",
                        function (_0x3ca055) {
                          (dialogConfirm(
                            "Relatório do Log:",
                            '<div id="textareaDebugLog" style="overflow: auto; height: 500px; width: 800px; padding: 5px;"></div>',
                          ),
                            setTimeout(function () {
                              $("#textareaDebugLog").html(_0x3ca055.data);
                            }, 0xc8));
                        },
                      )));
              } else
                alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+G", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x2de59f) {
              _0x2de59f.code
                ? ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  abrirConfiguracoes("GLOBAL"))
                : alert(
                    "Acesso negado! Somente o administrador tem acesso a esta opção.",
                  );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+R", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0x4b554c) {
              _0x4b554c.code
                ? ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  $(".idTabela:visible").each(function () {
                    if (isempty($(this).attr("tabela"))) returnfalse;
                    var _0x3f7475 = $(this).attr("tabela"),
                      _0x6d458d = $(this).parent();
                    if (_0x6d458d.find("> .idTabelaOverlay").size() == 0x0) {
                      var _0x3a4296 = { color: "gray" };
                      ($.each(
                        g.personalizacaoConf,
                        function (_0x411343, _0x1ab555) {
                          ((_0x1ab555 = "-" + _0x1ab555),
                            (strpos(_0x1ab555, _0x3f7475, 0x0) ||
                              strpos(
                                _0x1ab555,
                                g.empresaAtual + _0x3f7475,
                                0x0,
                              )) &&
                              (_0x3a4296 = { "font-weight": "bold" }));
                        },
                      ),
                        _0x6d458d.css("position", "relative"),
                        $("<div></div>")
                          .addClass("idTabelaOverlay")
                          .css({
                            position: "absolute",
                            top: 0x0,
                            left: 0x0,
                            width: _0x6d458d.outerWidth(),
                            height: _0x6d458d.outerHeight(),
                            "background-color": "black",
                            opacity: "0.45",
                          })
                          .appendTo(_0x6d458d),
                        $("<button></button>")
                          .addClass("idTabelaBtn")
                          .attr({ type: "button" })
                          .css({
                            position: "absolute",
                            top: _0x6d458d.outerHeight() / 0x2 - 0x19 + "px",
                            left: _0x6d458d.outerWidth() / 0x2 - 0x19 + "px",
                            "font-size": "20px",
                            cursor: "pointer",
                            "box-shadow": "0px 0px 3px 1px #A6A6A6",
                          })
                          .css(_0x3a4296)
                          .html($(this).attr("tabela"))
                          .click(function () {
                            ($(".idTabela:visible").each(function () {
                              var _0x49a2b1 = $(this).parent();
                              (_0x49a2b1.css("position", "static"),
                                _0x49a2b1.find("> .idTabelaOverlay").remove(),
                                _0x49a2b1.find("> .idTabelaBtn").remove());
                            }),
                              abrirConfiguracoes($(this).html()));
                          })
                          .appendTo(_0x6d458d));
                    } else
                      (_0x6d458d.css("position", "static"),
                        _0x6d458d.find("> .idTabelaOverlay").remove(),
                        _0x6d458d.find("> .idTabelaBtn").remove());
                  }))
                : alert(
                    "Acesso negado! Somente o administrador tem acesso a esta opção.",
                  );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+S", function () {
        $(".ctrlsbtn:visible:last").trigger("click.enviaForm");
      }),
      shortcut.add("Ctrl+Alt+Shift+E", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x4f3f6c) {
            if (_0x4f3f6c.code) {
              ((ctrlAltPressed = false), $("#ctrlAltPressed").remove());
              var _0x2f0f53 = $(".ui-dialog-content:visible:last").data(
                "janelaHref",
              );
              if (
                isset(g.janelasIgnorarESC[_0x2f0f53]) &&
                g.janelasIgnorarESC[_0x2f0f53] == 0x1
              ) {
                g.janelasIgnorarESC[_0x2f0f53] = 0x0;
                var _0x308773 = "Tecla ESC: AUTOMÁTICO";
              } else {
                g.janelasIgnorarESC[_0x2f0f53] = 0x1;
                var _0x308773 = "Tecla ESC: CONFIRMAÇÃO";
              }
              var _0x54d4fc = $(
                '<span class="idTabelaBtn" type="button" style="position: absolute; top: 0px; left: 0px; padding: 10px 20px; font-size: 30px; font-weight: bold; background-color: #393939; color: white; width: 100%; height: 100%; opacity: 0.9;">' +
                  _0x308773 +
                  "</span>",
              );
              (_0x54d4fc.appendTo($(".ui-dialog-content:visible:last")),
                setTimeout(function () {
                  _0x54d4fc.remove();
                }, 0x7d0),
                $.ajax({
                  url: "/sistema/funcoes/janelasIgnorarESC/salvar/",
                  dataType: "html",
                  data:
                    "ajax=true&json=" +
                    encodeURIComponent(JSON.stringify(g.janelasIgnorarESC)),
                  type: "post",
                  success: function (_0x4b4d76) {},
                }));
            } else
              alert(
                "Acesso negado! Somente o administrador tem acesso a esta opção.",
              );
          },
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+U", function () {
        return (
          $.getJSON(
            "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
            function (_0xe3eec8) {
              _0xe3eec8.code
                ? ($("#ctrlAltPressed").remove(),
                  (ctrlAltPressed = false),
                  $.getJSON(
                    "/sistema/funcoes/informacoes/listaPersonalizacoes/?ajax=true&callback=?",
                    function (_0x426f30) {
                      var _0xf51f1e = unique();
                      (dialogConfirm(
                        "Personalizações",
                        '<textarea id="textareaGrid-' +
                          _0xf51f1e +
                          '" style="overflow: auto; height: 600px; width: 1000px;"></textarea>',
                      ),
                        setTimeout(function () {
                          $("#textareaGrid-" + _0xf51f1e).html(_0x426f30.data);
                        }, 0xc8));
                    },
                  ))
                : alert(
                    "Acesso negado! Somente o administrador tem acesso a esta opção.",
                  );
            },
          ),
          false
        );
      }),
      shortcut.add("Ctrl+Alt+T", function () {
        abrirJanela(
          false,
          divIdRootAberto(),
          "",
          menuId("tarefas"),
          "",
          "Tarefas",
          "/etc/etc017/content/etc017ContentTarefas/",
          "",
          [0x3de, 0x2bc],
        );
      }),
      shortcut.add("Ctrl+Alt+Shift+V", function () {
        $.getJSON(
          "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
          function (_0x9cf0b5) {
            _0x9cf0b5.code
              ? ((ctrlAltPressed = false),
                $("#ctrlAltPressed").remove(),
                $.getJSON(
                  "/sistema/ajax/modoDesenvolvedor/?ajax=true&callback=?",
                  function (_0x42cd63) {
                    _0x42cd63.code ? location.reload() : alert(_0x42cd63.data);
                  },
                ))
              : alert(
                  "Acesso negado! Somente o administrador tem acesso a esta opção.",
                );
          },
        );
      }),
      shortcut.add(
        "Ctrl+C",
        function () {
          if (
            $(".telaEditorHtml:visible:last")
              .find(".telaItem.ui-multidraggable")
              .size() > 0x0
          ) {
            let _0x593244 = "";
            $(".telaEditorHtml:visible:last")
              .find(".telaItem.ui-multidraggable")
              .each(function () {
                _0x593244 += $(this).get(0x0).outerHTML;
              });
            if (navigator.clipboard === undefined) {
              if ($("#editorTelaCopiaCola").size() > 0x0) returntrue;
              (dialogConfirm(
                "Copiar",
                '<textarea id="editorTelaCopiaCola" style="width: 500px; height: 200px;"></textarea>',
                {
                  Fechar: function () {
                    $(this).dialog("destroy").remove();
                  },
                },
              ),
                setTimeout(function () {
                  $("#editorTelaCopiaCola").val(_0x593244);
                }, 0xc8));
            } else navigator.clipboard.writeText(_0x593244);
            returnfalse;
          } else returntrue;
        },
        { propagate: true },
      ),
      shortcut.add(
        "Ctrl+V",
        async function () {
          const _0x160924 = $(".telaEditorHtml:visible:last");
          if (_0x160924.size() > 0x0) {
            const _0x235e41 = function (_0x10a054) {
              const _0x37f26b = $(_0x10a054);
              (_0x37f26b.removeClass("ui-multidraggable"),
                _0x37f26b
                  .find(".ui-draggable")
                  .removeClass("ui-multidraggable"),
                _0x37f26b.appendTo($(".telaEditorHtml:visible:last")),
                window[_0x160924.data("tipo") + "editorTela"](
                  _0x160924.data("areaid"),
                ));
            };
            if (navigator.clipboard === undefined) {
              if ($("#editorTelaCopiaCola").size() > 0x0) returntrue;
              dialogConfirm(
                "Copiar",
                '<textarea id="editorTelaCopiaCola" style="width: 500px; height: 200px;"></textarea>',
                {
                  Colar: function () {
                    (_0x235e41($("#editorTelaCopiaCola").val()),
                      $(this).dialog("destroy").remove());
                  },
                  Fechar: function () {
                    $(this).dialog("destroy").remove();
                  },
                },
              );
            } else {
              const _0x538299 = await navigator.clipboard.readText();
              _0x235e41(_0x538299);
            }
            returnfalse;
          } else returntrue;
        },
        { propagate: true },
      ),
      !isempty(_0x5b6be8) &&
        $("<div>")
          .css({
            width: "100%",
            position: "fixed",
            top: "250px",
            "text-align": "center",
            color: "white",
            "font-weight": "normal",
            "font-size": "20px",
            "background-color": "gray",
            padding: "15px 0",
          })
          .html(
            "Edição do menu está ativo!<br />Para desativar, pressione:<br />CTRL + ALT + SHIFT + M",
          )
          .appendTo("body"));
  }
  (shortcut.add(
    "Ctrl+Delete",
    function (_0x251758) {
      $('input[value="Limpar"]:visible:last').trigger("click");
    },
    { propagate: false },
  ),
    shortcut.add(
      "Ctrl+Left",
      function (_0x21a266) {
        var _0x2b2cde = $(".tabs:visible:last").find(
          ".ui-tabs-nav > li.ui-tabs-selected",
        );
        do {
          _0x2b2cde = _0x2b2cde.prev();
        } while (_0x2b2cde.size() > 0x0 && !_0x2b2cde.is(":visible"));
        var _0xa55d8e =
          typeof _0x2b2cde.attr("index") == "undefined"
            ? _0x2b2cde.index()
            : parseInt(_0x2b2cde.attr("index"));
        $(".tabs:visible:last").tabs("select", _0xa55d8e);
      },
      { propagate: false },
    ),
    shortcut.add(
      "Ctrl+Right",
      function (_0x183f04) {
        var _0x450a01 = $(".tabs:visible:last").find(
          ".ui-tabs-nav > li.ui-tabs-selected",
        );
        do {
          _0x450a01 = _0x450a01.next();
        } while (_0x450a01.size() > 0x0 && !_0x450a01.is(":visible"));
        var _0x625994 =
          typeof _0x450a01.attr("index") == "undefined"
            ? _0x450a01.index()
            : parseInt(_0x450a01.attr("index"));
        $(".tabs:visible:last").tabs("select", _0x625994);
      },
      { propagate: false },
    ),
    shortcut.add(
      "Up",
      function (_0x243327) {
        eventosTeclado("Up", _0x243327);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Down",
      function (_0x526d6a) {
        eventosTeclado("Down", _0x526d6a);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Right",
      function (_0x2c608b) {
        eventosTeclado("Right", _0x2c608b);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Left",
      function (_0x214453) {
        eventosTeclado("Left", _0x214453);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Page_up",
      function (_0x2291c2) {
        eventosTeclado("Page_up", _0x2291c2);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Page_down",
      function (_0x2f1ed0) {
        eventosTeclado("Page_down", _0x2f1ed0);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Enter",
      function (_0x5df277) {
        return eventosTeclado("Enter", _0x5df277);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Home",
      function (_0x103a0d) {
        eventosTeclado("Home", _0x103a0d);
      },
      { propagate: true },
    ),
    shortcut.add(
      "End",
      function (_0x3db309) {
        eventosTeclado("End", _0x3db309);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Esc",
      function (_0x280500) {
        eventosTeclado("Esc", _0x280500);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Alt+A",
      function (_0x2c63c3) {
        eventosTeclado("Alt_E", _0x2c63c3);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Alt+D",
      function (_0x1b8d89) {
        eventosTeclado("Alt_D", _0x1b8d89);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Tab",
      function (_0x23eac5) {
        eventosTeclado("Tab", _0x23eac5);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Shift+Tab",
      function (_0x55882c) {
        eventosTeclado("Shift_Tab", _0x55882c);
      },
      { propagate: true },
    ),
    shortcut.add(
      "Alt+M",
      function (_0x37a0f8) {
        if (_0x37a0f8.code == "NumpadSubtract") return;
        var _0x5196d8 = $("#topoMenu #barra li a.hover");
        _0x5196d8.size() == 0x0
          ? $("#topoMenu #barra .menuvertical:eq(0) a:first").trigger(
              "mouseover",
            )
          : _0x5196d8.trigger("mouseout");
      },
      { propagate: false },
    ),
    shortcut.add("Alt+C", abrirMenuPorNome("Cadastros"), { propagate: false }),
    shortcut.add("Alt+V", abrirMenuPorNome("Vendas"), { propagate: false }),
    shortcut.add("Alt+E", abrirMenuPorNome("Estoque"), { propagate: false }),
    shortcut.add("Alt+F", abrirMenuPorNome("Financeiro"), { propagate: false }),
    shortcut.add("Alt+L", abrirMenuPorNome("Fiscal"), { propagate: false }),
    shortcut.add("Alt+X", abrirMenuPorNome("Comex"), { propagate: false }),
    shortcut.add("Alt+G", abrirMenuPorNome("Gerencial"), { propagate: false }),
    shortcut.add("Alt+T", abrirMenuPorNome("Outros"), { propagate: false }),
    shortcut.add("Alt+O", abrirMenuPorNome("Configuração"), {
      propagate: false,
    }),
    shortcut.add("Alt+S", abrirMenuPorNome("Sair"), { propagate: false }),
    shortcut.add(
      "Alt+F5",
      function (_0x34deb9) {
        (($grid = $(".grid:visible:last")),
          $grid.size() > 0x0 && gridRefresh($grid));
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+Right",
      function (_0x132f76) {
        (($gridFiltro = $(":focus").closest(".gridFiltro")),
          $gridFiltro.size() > 0x0 &&
            $gridFiltro.find(".gridFiltroBotoes button:first").focus());
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+Left",
      function (_0x413b27) {
        (($gridFiltro = $(":focus").closest(".gridFiltro")),
          $gridFiltro.size() > 0x0 &&
            $gridFiltro.find("input:visible:not([disabled]):first").focus());
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+Plus",
      function (_0x5c9bd7) {
        ($(":focus").blur(),
          ($gridMaxBtn = $(".gridTheadMaximizar:visible:last")),
          $gridMaxBtn.find("span").hasClass("ui-icon-triangle-1-n") &&
            $gridMaxBtn.trigger("click"));
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+Minus",
      function (_0x3fd431) {
        ($(":focus").blur(),
          ($gridMaxBtn = $(".gridTheadMaximizar:visible:last")),
          $gridMaxBtn.find("span").hasClass("ui-icon-triangle-1-s") &&
            $gridMaxBtn.trigger("click"));
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+PlusNum",
      function (_0x1690c5) {
        ($(":focus").blur(),
          ($gridMaxBtn = $(".gridTheadMaximizar:visible:last")),
          $gridMaxBtn.find("span").hasClass("ui-icon-triangle-1-n") &&
            $gridMaxBtn.trigger("click"));
      },
      { propagate: false },
    ),
    shortcut.add(
      "Alt+MinusNum",
      function (_0x35d1d9) {
        ($(":focus").blur(),
          ($gridMaxBtn = $(".gridTheadMaximizar:visible:last")),
          $gridMaxBtn.find("span").hasClass("ui-icon-triangle-1-s") &&
            $gridMaxBtn.trigger("click"));
      },
      { propagate: false },
    ),
    $("#messageAlert .messages li[class*='alertidrootcache']").live({
      mouseover: function () {
        !$(this).hasClass("notifOculta") &&
          $(this).find("img").css("display", "inline");
      },
      mouseout: function () {
        !$(this).hasClass("notifOculta") &&
          $(this).find("img").css("display", "none");
      },
    }));
  if (!g.oneWindow) {
    var _0x4cb777 = (function () {
        var _0x3fc149,
          _0x559e1b,
          _0x4b05eb = {
            hidden: "visibilitychange",
            webkitHidden: "webkitvisibilitychange",
            mozHidden: "mozvisibilitychange",
            msHidden: "msvisibilitychange",
          };
        for (_0x3fc149 in _0x4b05eb) {
          if (_0x3fc149 in document) {
            _0x559e1b = _0x4b05eb[_0x3fc149];
            break;
          }
        }
        return function (_0x2ab891) {
          if (_0x2ab891) document.addEventListener(_0x559e1b, _0x2ab891);
          return !document[_0x3fc149];
        };
      })(),
      _0xb17ae = function () {
        $.getJSON(
          "/sistema/funcoes/util/verificaEmpresaAtual/?ajax=true" +
            "&callback=?",
          function (_0x59eaad) {
            (_0x59eaad != g.empresaAtual &&
              ((g.empresaAtual = _0x59eaad),
              $.ajax({
                url: "/empresa/" + _0x59eaad + "/",
                dataType: "html",
                success: function (_0x5ceb74) {
                  window.location = "/erp";
                },
              })),
              setTimeout(_0xb17ae, 0x2710));
          },
        );
      };
    _0xb17ae();
    var _0x3c618b, _0x57152f, _0x38c2c3, _0x25928c, _0xbbe6cb;
    ((funcoesMensagens.notificacao = function () {
      ($.getJSON(
        "/sistema/funcoes/mensagens/montarNotificacoes/?ajax=true&suporte=true&callback=?",
        function (_0x57586b) {
          if (_0x57586b === undefined)
            return (
              console.log("Erro no retorno ajax montarNotificacoes suporte"),
              false
            );
          _0x57586b.code
            ? eval(_0x57586b.data)
            : console.log("Retorno ajax: " + _0x57586b.data);
        },
      ).always(function () {
        $.getJSON(
          "/sistema/funcoes/mensagens/montarNotificacoes/?ajax=true&callback=?",
          function (_0x5a821b) {
            ((contadorAnterior = $("#messageAlert .alerts").text()),
              (contador = 0x0));
            if (_0x5a821b === undefined)
              return (
                console.log("erro no retorno ajax montarNotificacoes"),
                false
              );
            (_0x5a821b.code
              ? eval(_0x5a821b.data)
              : console.log("Retorno ajax:" + _0x5a821b.data),
              contadorAnterior != ""
                ? contadorAnterior < contador
                  ? ($("#messageAlert .alerts").css({
                      "background-color": "red",
                      background:
                        "linear-gradient(center top , #FA3C45, #DC0D17) repeat scroll 0 0 transparent;",
                      "font-size": "14px",
                    }),
                    $("#messageAlert .alerts")
                      .html(parseInt(contador))
                      .fadeIn())
                  : ($("#messageAlert .alerts").css({
                      "background-color": "none",
                      "font-size": "14px",
                    }),
                    $("#messageAlert .alerts")
                      .html(parseInt(contador))
                      .fadeIn())
                : ($("#messageAlert .alerts").css({
                    "background-color": "red",
                    background:
                      "linear-gradient(center top , #FA3C45, #DC0D17) repeat scroll 0 0 transparent;",
                    "font-size": "14px",
                  }),
                  $("#messageAlert .alerts")
                    .html(parseInt(contador))
                    .fadeIn()));
          },
        );
      }),
        _0x4cb777()
          ? (_0x57152f = setTimeout(funcoesMensagens.notificacao, 0x4e20))
          : clearInterval(_0x57152f));
    }),
      funcoesMensagens.notificacao(),
      _0x4cb777(funcoesMensagens.notificacao),
      (funcoesMensagens.chat = function () {
        $.getJSON(
          "/sistema/funcoes/chat/verificarMensagem/?ajax=true" + "&callback=?",
          function (_0x4be219) {
            (_0x4be219.code
              ? eval(_0x4be219.data)
              : console.log(_0x4be219.data),
              _0x4cb777()
                ? (_0x25928c = setTimeout(funcoesMensagens.chat, 0xea60))
                : clearInterval(_0x25928c));
          },
        );
      }),
      funcoesMensagens.chat(),
      _0x4cb777(funcoesMensagens.chat),
      setInterval(function () {
        var _0x4eaf02 = new Date().getTime();
        $(".grid:visible").each(function () {
          var _0x57a5b7 = $(this),
            _0x40f581 = _0x57a5b7.data("lastTimeUpdate");
          if (typeof _0x40f581 == "undefined")
            _0x57a5b7.data("lastTimeUpdate", _0x4eaf02);
          else {
            if (_0x40f581 == "never") {
            } else
              _0x4eaf02 - 0x36ee80 > _0x40f581 &&
                (_0x57a5b7.data("lastTimeUpdate", _0x4eaf02),
                gridRefresh(_0x57a5b7));
          }
        });
      }, 0x7d0),
      acoesNotificacoes(),
      opcoesZoom());
  }
  (($.ui.dialog.prototype.destroy_ = $.ui.dialog.prototype.destroy),
    ($.ui.dialog.prototype.destroy = function () {
      (this["element"].dialog("destroy_"),
        $(".ui-dialog-content:visible:last").dialog("moveToTop"));
    }),
    $("body")
      .unbind("click.menuOut")
      .bind("click.menuOut", function (_0x11bc67) {
        var _0x3eb22b = $(_0x11bc67.target),
          _0x4ea74a = $("#topoMenu #barra li a.hover");
        if (_0x4ea74a.size() != 0x0)
          return (
            !_0x3eb22b.closest("li").hasClass("menuvertical") &&
              _0x4ea74a.trigger("mouseout"),
            true
          );
        var _0x3ffc9d = _0x3eb22b.hasClass("td")
          ? _0x3eb22b
          : _0x3eb22b.closest("td");
        selecionarCelula(_0x3ffc9d);
      }),
    $("body")
      .unbind("click.colunaAcoes")
      .bind("click.colunaAcoes", function (_0x5688c2) {
        $(".colunaAcoes").remove();
      }),
    document.addEventListener(
      "contextmenu",
      function (_0x353730) {
        $(".colunaAcoes").remove();
        if (_0x353730 && _0x353730.target) {
          $target = $(_0x353730.target);
          if (
            $target.hasClass("ui-button") ||
            $target.hasClass("ui-button-text") ||
            $target.parent().hasClass("ui-button-text")
          ) {
            $elem = $target.hasClass("ui-button")
              ? $target
              : $target.closest(".ui-button");
            const _0x16fe93 = gridPegarDivId($target),
              _0x27b897 = $("#" + _0x16fe93);
            if (_0x27b897.data("href").match(/\/grid\//)) {
              var _0xc0b092 = $("<ul></ul>")
                .addClass("dropdown-menu colunaAcoes")
                .attr("role", "menu")
                .css({
                  display: "block",
                  top: top,
                  left: 0x0,
                  position: "absolute",
                });
              ($(
                '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="wikiAjuda(\'' +
                  _0x16fe93 +
                  "', 'GRID', 'BOTAO', '" +
                  btoa($elem.attr("id")) +
                  "', '" +
                  $elem.text() +
                  "'); $('.colunaAcoes').remove(); event.stopPropagation(); event.preventDefault(); return false;\">Ajuda</a></li>",
              ).appendTo(_0xc0b092),
                $(
                  '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="event.stopPropagation(); $(\'.colunaAcoes\').remove(); return false;">Fechar</a></li>',
                ).appendTo(_0xc0b092),
                _0xc0b092.appendTo($elem),
                _0x353730.preventDefault());
            }
          }
        }
      },
      false,
    ),
    abrirJanela(
      event,
      "Cockpit",
      "cockpit",
      menuId("Cockpit"),
      "",
      "",
      "/etc/etc011/content/etc011ContentCockpit/",
      "",
      "full",
      false,
      "topleft",
      false,
    ));
});
var desktopHeight,
  desktopWidth,
  desktopUp = 0x33,
  desktopUpFull = 0x33,
  desktopDown = 0x0,
  desktopLeft = 0x0,
  desktopRight = 0x0,
  toolbarLeft = 0x28,
  toolbarRight = 0x28,
  dialogBorder = 0x2,
  zIndex = 0x3e9,
  closeLastBtn = true,
  spaceBtw = 0x0,
  ctrlAltPressed = false,
  windows = {},
  windowsHistory = [],
  menuUniques = {},
  oneWindow = true,
  currentFocus = null,
  prevenirColunaEditarDuplo = false,
  diagramaNodeDataArray = [],
  diagramaLinkDataArray = [],
  diagramaProgs = [],
  myDiagram = {},
  diagramaId = "",
  messageAlertHover = false,
  fontSizeOption = false,
  codBarraLastTime = 0x0,
  codBarraScan = "",
  codBarraLendo = false,
  alertasRemovidos = [],
  abrirJanelaPermissao = "",
  funcoesMensagens = {},
  canvasImg,
  notificacoesSuporte = [],
  ultimoCampoTab = [],
  menuOut = true,
  menuKeyboardTimeout = false,
  linhaSelecionadaModoCelula = true,
  colunaSelecionadaBackgroud = "",
  cabecalhoeditorTelaItemSelecionado = false,
  corpoeditorTelaItemSelecionado = false,
  rodapeeditorTelaItemSelecionado = false,
  contadorAnterior,
  contador = 0x0,
  requisicoesAjax = {};
function fixDesktopSize() {
  $("#desktop").css("top") != "0px" &&
    $("#desktop").css("top") != "auto" &&
    (oneWindow = false);
  var _0x118e19 = $("#desktop");
  (_0x118e19.css({ width: "100%", height: "100%" }),
    (desktopHeight = _0x118e19.height()),
    (desktopWidth = _0x118e19.width()),
    !oneWindow
      ? (_0x118e19.height(desktopHeight - (desktopUp + desktopDown)),
        _0x118e19.width(desktopWidth),
        (desktopHeight = _0x118e19.height()))
      : ((desktopHeight = desktopHeight - 0x4),
        (desktopWidth = desktopWidth - 0x11)));
}
function acoesNotificacoes() {
  ($("#messageAlert .click")
    .unbind()
    .click(function (_0x21d72d) {
      ($("#messageAlert").css("z-index", 0x1869f),
        $("#messageAlert .messages span:first").html("Notificações"),
        $("#messageAlert .mensagemenviada").hide(),
        $("#messageAlert .body").show(),
        $("#messageAlert .body .itemAgendado").remove(),
        $("#messageAlert .alerts").data("piscando", 0x0),
        $("#messageAlert .alerts").css({
          "font-size": "14px",
          background: "none",
        }));
      if (!messageAlertHover)
        ($("#messageAlert .boxenviarmensagem").hide(),
          $("#messageAlert .messages").show(),
          (messageAlertHover = true),
          $("body")
            .unbind("click.message")
            .bind("click.message", function (_0x51f04e) {
              ($("#messageAlert .alerts").data("piscando", 0x0),
                $("#messageAlert .alerts").css("font-size", "14px"));
              if (
                !$(_0x51f04e.target).hasClass("icone") &&
                !$(_0x51f04e.target).hasClass("alerts") &&
                !$(_0x51f04e.target).hasClass("exibirOcultarNotificacoes") &&
                !$(_0x51f04e.target).hasClass("refreshNotifications") &&
                $(_0x51f04e.target).closest(".ui-datepicker-calendar").size() ==
                  0x0
              ) {
                ($("#messageAlert").css("z-index", 0x1),
                  $("#messageAlert .messages").hide(),
                  $("#messageAlert .exibirOcultarNotificacoes")
                    .attr({
                      src: "/hardness3/static/img/check.png",
                      title: "Exibir notificações ocultas",
                    })
                    .css({
                      "-webkit-filter": "opacity(70%)",
                      filter: "opacity(70%)",
                    })
                    .removeClass("exibindo"));
                var _0x4eb571 = $("#messageAlert .messages li.msgalert").each(
                  function () {
                    var _0x595cf0 = $(this).attr("style");
                    !_0x595cf0.match(/lightgreen/) && $(this).attr("style", "");
                  },
                );
                ((messageAlertHover = false),
                  $("body").unbind("click.message"));
              }
            }),
          $("#messageAlert .messages .body").css("height", "auto"),
          $("#messageAlert .messages .body").height() >
            $("#desktop").height() - 0x32 &&
            $("#messageAlert .messages .body").height(
              $("#desktop").height() - 0x32,
            ));
      else {
        ($("#messageAlert").css("z-index", 0x1),
          $("#messageAlert .messages").hide());
        var _0x4933fa = $("#messageAlert .messages li.msgalert").each(
          function () {
            var _0x296083 = $(this).attr("style");
            !_0x296083.match(/lightgreen/) && $(this).attr("style", "");
          },
        );
        ((messageAlertHover = false), $("body").unbind("click.message"));
      }
    }),
    $("#messageAlert .enviarMensagem")
      .unbind()
      .click(function (_0x32f679) {
        (acaoListarAgendadas(),
          $("#messageAlert .messages span:first").html("Enviar uma Mensagem"),
          $("#messageAlert .body").show());
        var _0x532e80 = $("#messageAlert .boxenviarmensagem");
        if (_0x532e80.is(":visible"))
          (setTimeout(function () {
            $("#messageAlert .body .itemAgendado").slideUp("fast");
          }, 0x64),
            $("#messageAlert .messages span:first").html("Notificações"),
            $("#messageAlert .boxenviarmensagem").slideUp("normal"));
        else {
          $("#messageAlert .messages span:first").html("Enviar uma Mensagem");
          var _0x20fd68 = $("#messageAlert .body .campoData");
          (_0x20fd68.datepicker({ dateFormat: "dd/mm/yy" }),
            _0x20fd68.datepicker("option", "dayNamesMin", [
              "Dom",
              "Seg",
              "Ter",
              "Qua",
              "Qui",
              "Sex",
              "Sab",
            ]),
            _0x20fd68.datepicker("option", "monthNames", [
              "Janeiro",
              "Fevereiro",
              "Março",
              "Abril",
              "Maio",
              "Junho",
              "Julho",
              "Agosto",
              "Setembro",
              "Outubro",
              "Novembro",
              "Dezembro",
            ]),
            $(".ui-datepicker-div")
              .unbind("click")
              .bind("click", function (_0x27cceb) {
                returnfalse;
              }),
            $("#messageAlert .body").slideDown("normal", function () {
              (_0x532e80.find(".campoData").val(""),
                _0x532e80.find(".campoTempo").val("00:00"),
                _0x532e80.find("select").val(g.usuarioAtual),
                _0x532e80.find("textarea").val(""));
            }),
            _0x532e80.find("select").val(0x0),
            _0x532e80.find("textarea").val(""),
            $("#messageAlert .boxenviarmensagem").slideDown(
              "normal",
              function () {
                $("#messageAlert .boxenviarmensagem select").focus();
              },
            ));
        }
        returnfalse;
      }),
    $("#messageAlert .boxenviarmensagem")
      .unbind()
      .click(function (_0x489fa5) {
        if (_0x489fa5.target.nodeName != "INPUT") returnfalse;
        else _0x489fa5.stopPropagation();
      }),
    $("#messageAlert .enviarSuporte")
      .unbind()
      .click(function (_0x1f81ae) {
        abrirEmailSuporte();
      }),
    $("#topo .notificacoesSuporte")
      .unbind()
      .click(function (_0x5933cf) {
        abrirEmailSuporte();
      }),
    $("#messageAlert .refreshNotifications")
      .unbind()
      .click(function (_0x137570) {
        atualizarNotificacoes();
      }),
    $("#messageAlert .exibirOcultarNotificacoes")
      .unbind()
      .click(function (_0x3c3585) {
        ($(this).hasClass("exibindo")
          ? ($(this)
              .attr({
                src: "/hardness3/static/img/check.png",
                title: "Exibir notificações desabilitadas",
              })
              .css({ "-webkit-filter": "opacity(70%)", filter: "opacity(70%)" })
              .removeClass("exibindo"),
            $(
              "#messageAlert .messages li[class*='alertidrootcache'].notifOculta",
            ).each(function (_0x1e3557, _0x3e861e) {
              $(_0x3e861e).css("display", "none");
            }))
          : ($(this)
              .attr({
                src: "/hardness3/static/img/remove.png",
                title: "Remover notificações desabilitadas",
              })
              .css({ "-webkit-filter": "opacity(70%)", filter: "opacity(70%)" })
              .addClass("exibindo"),
            $("#messageAlert .messages li[class*='alertidrootcache']").each(
              function (_0x262519, _0x4b57bd) {
                $(_0x4b57bd).css("display", "flex");
              },
            )),
          $("#messageAlert .messages .body").css("height", "auto"),
          $("#messageAlert .messages .body").height() >
            $("#desktop").height() - 0x32 &&
            $("#messageAlert .messages .body").height(
              $("#desktop").height() - 0x32,
            ));
      }),
    $("#messageAlert .boxenviarmensagem button")
      .unbind()
      .click(function (_0x29fc55) {
        var _0x2e8962 = $("#messageAlert .boxenviarmensagem"),
          _0x213fcb = _0x2e8962.find("select").val(),
          _0x275674 = _0x2e8962.find("textarea").val(),
          _0x4ec043 = _0x2e8962.find("input").prop("checked") ? "S" : "N",
          _0x5a3e00 = _0x2e8962.find(".campoTempo").val(),
          _0x59636b = _0x2e8962.find(".campoData").val();
        return (
          isempty(_0x213fcb) || isempty(_0x275674) || _0x213fcb <= 0x0
            ? ($("#messageAlert .boxenviarmensagem span").show(),
              setTimeout(function () {
                $("#messageAlert .boxenviarmensagem span").hide();
              }, 0x7d0))
            : $.ajax({
                url: "/sistema/funcoes/chat/enviarMensagem/",
                dataType: "html",
                data:
                  "ajax=true&C007_Id=" +
                  encodeURIComponent(_0x213fcb) +
                  "&Mensagem=" +
                  encodeURIComponent(_0x275674) +
                  "&travar=" +
                  encodeURIComponent(_0x4ec043) +
                  "&Tempo=" +
                  encodeURIComponent(_0x5a3e00) +
                  "&Data=" +
                  encodeURIComponent(_0x59636b),
                type: "post",
                success: function (_0x152ada) {
                  return (
                    $("#messageAlert .boxenviarmensagem").slideUp(
                      "fast",
                      function () {
                        ($("#messageAlert .messages span:first").html(
                          "Notificações",
                        ),
                          $("#messageAlert .mensagemenviada").slideDown(
                            "fast",
                          ));
                        isempty(_0x59636b) &&
                          setTimeout(function () {
                            $("#messageAlert .body .itemAgendado").slideUp(
                              "fast",
                            );
                          }, 0x64);
                        (setTimeout(function () {
                          $("#messageAlert .mensagemenviada").slideUp("fast");
                        }, 0x7d0),
                          acaoListarAgendadas());
                        var _0x5153b8 = $("#messageAlert .boxenviarmensagem");
                        (_0x5153b8.find(".campoData").val(""),
                          _0x5153b8.find(".campoTempo").val("00:00"),
                          _0x5153b8.find("select").val(g.usuarioAtual),
                          _0x5153b8.find("textarea").val(""));
                      },
                    ),
                    false
                  );
                },
              }),
          false
        );
      }));
}
$(function () {
  $.getJSON("/sistema/funcoes/senha/status/?ajax=true").done(
    function (_0x23f33c) {
      if (!_0x23f33c || !_0x23f33c.code || typeof dialogConfirm === "undefined")
        return;
      if (!$("#segurancaSenhaEstilo").length)
        $(
          '<style id="segurancaSenhaEstilo">.senha-dialogo{max-width:530px;color:#243047;line-height:1.5;font-family:Arial,sans-serif}.senha-dialogo-modal{border:0!important;border-radius:18px!important;overflow:hidden;box-shadow:0 24px 70px rgba(15,23,42,.38)!important}.senha-dialogo-modal .ui-dialog-titlebar{background:linear-gradient(125deg,#0f3d75,#1769aa)!important;border:0!important;border-radius:0!important;padding:17px 22px!important;color:#fff!important;font-size:19px!important}.senha-dialogo-modal .ui-dialog-content{padding:22px 24px 14px!important;background:#f8fafc!important}.senha-dialogo-modal .ui-dialog-buttonpane{margin:0!important;padding:14px 20px 18px!important;background:#f8fafc!important;border:0!important}.senha-dialogo-modal .ui-dialog-buttonpane button{border-radius:7px!important;padding:8px 14px!important;border:1px solid #cbd5e1!important;background:#fff!important;color:#334155!important;font-weight:bold!important}.senha-dialogo-modal .ui-dialog-buttonpane button:first-child{background:#1463a5!important;border-color:#1463a5!important;color:#fff!important;box-shadow:0 4px 10px rgba(20,99,165,.25)!important}.senha-dialogo__icone{float:right;width:44px;height:44px;line-height:44px;text-align:center;border-radius:50%;background:#e0f2fe;color:#075985;font-size:23px;margin:0 0 8px 14px}.senha-dialogo__destaque{clear:both;background:#fff7ed;border-left:5px solid #f97316;border-radius:8px;padding:15px 17px;margin:12px 0 16px;font-size:19px;font-weight:bold}.senha-dialogo__destaque--urgente{background:#fff1f2;border-left-color:#e11d48;color:#881337}.senha-dialogo__titulo{font-size:19px;font-weight:bold;margin:2px 0 6px;color:#102a43}.senha-dialogo__regras{margin:12px 0 8px;padding:10px 12px;background:#eef6ff;border-radius:8px;font-size:13px}.senha-dialogo label{display:block;font-weight:bold;margin:12px 0 4px}.senha-dialogo input{box-sizing:border-box;width:100%;padding:10px;border:1px solid #b9c6d5;border-radius:6px;font-size:14px}.senha-dialogo input[readonly]{background:#eef2f7;color:#526173;cursor:not-allowed}.senha-dialogo__campo-senha{position:relative}.senha-dialogo__campo-senha input{padding-right:48px}.senha-dialogo__ver-senha{position:absolute;right:5px;top:5px;border:0!important;background:transparent!important;color:#1769aa!important;padding:4px 7px!important;box-shadow:none!important;font-size:17px!important;cursor:pointer}.senha-dialogo__erro{display:none;background:#fff1f2;color:#b42318;border:1px solid #fecdd3;border-radius:6px;padding:10px;margin-top:12px}.senha-dialogo__sucesso{background:#ecfdf3;color:#067647;border-left:5px solid #12b76a;border-radius:7px;padding:14px}.senha-dialogo__email{margin:16px 0 0;border-top:1px solid #dbe4ee;padding-top:12px}.senha-dialogo__email-edicao{display:none}.senha-dialogo__link{color:#1463a5;text-decoration:underline;cursor:pointer;font-weight:bold}</style>',
        ).appendTo("head");
      if (!$("#segurancaSenhaEstiloAjuste").length)
        $(
          '<style id="segurancaSenhaEstiloAjuste">.senha-dialogo-modal{padding:0!important;background:#1769aa!important}.senha-dialogo-modal .ui-dialog-titlebar{margin:0!important;border-radius:18px 18px 0 0!important}.senha-dialogo-modal .ui-dialog-content{position:relative!important}.senha-dialogo__icone{position:absolute!important;right:24px!important;top:22px!important;float:none!important;margin:0!important}.senha-dialogo__titulo{padding-right:64px!important}</style>',
        ).appendTo("head");
      if (!$("#segurancaSenhaEstiloLoading").length)
        $(
          '<style id="segurancaSenhaEstiloLoading">.senha-dialogo__processando{background:#eef6ff;color:#075985;border:1px solid #bfdbfe;border-radius:6px;padding:10px;margin-top:12px;font-weight:bold}.senha-dialogo-modal--obrigatoria .ui-dialog-buttonpane button:first-child{background:#16803a!important;border-color:#16803a!important;box-shadow:0 4px 10px rgba(22,128,58,.28)!important}</style>',
        ).appendTo("head");
      var _0x4e17e5 =
          _0x23f33c.diasRestantes < 0x0
            ? "Sua senha expirou há " +
              Math.abs(_0x23f33c.diasRestantes) +
              " dia(s)."
            : _0x23f33c.diasRestantes === 0x0
              ? "Sua senha expirou hoje."
              : _0x23f33c.diasRestantes === 0x1
                ? "Sua senha expira em 1 dia."
                : "Sua senha expira em " + _0x23f33c.diasRestantes + " dias.",
        _0x3b10b4 = $("<div>")
          .text(_0x23f33c.email || "Não informado")
          .html();
      function _0xa488e8(_0x31f22b) {
        var _0x323af5 = String(_0x31f22b || "").split("@"),
          _0x3a7683,
          _0x3f6541,
          _0xa4d84f,
          _0x2425ff;
        if (_0x323af5.length !== 0x2) return "***";
        return (
          (_0x3f6541 =
            _0x323af5[0x0].substr(0x0, Math.min(0x2, _0x323af5[0x0].length)) ||
            "*"),
          (_0x3a7683 = _0x323af5[0x1].split(".")),
          (_0xa4d84f = _0x3a7683[0x0].substr(0x0, 0x1) || "*"),
          (_0x2425ff =
            _0x3a7683.length > 0x1 ? "." + _0x3a7683.slice(0x1).join(".") : ""),
          $("<div>")
            .text(_0x3f6541 + "****@" + _0xa4d84f + "****" + _0x2425ff)
            .html()
        );
      }
      var _0x1b61f9 = $("<div>")
          .text(_0x23f33c.nome || "usuário")
          .html(),
        _0x4e34c1 = parseInt(_0x23f33c.prazoVencimentoDias, 0xa) || 0x78,
        _0x238491 = _0x23f33c.diasRestantes <= 0x3,
        _0x3ac7e4 = _0x23f33c.obrigatoria
          ? "Para continuar acessando o sistema, altere sua senha."
          : _0x238491
            ? "Por medida de segurança, a senha deve ser renovada a cada " +
              _0x4e34c1 +
              " dias. Após esta data, será necessário alterá-la para continuar acessando o sistema."
            : "Por medida de segurança, a senha deve ser renovada a cada " +
              _0x4e34c1 +
              " dias. Recomendamos alterar agora para evitar a interrupção do acesso após a data de vencimento.",
        _0x204715 =
          '<div class="senha-dialogo"><div class="senha-dialogo__icone">🔒</div><p>Olá, <strong>' +
          _0x1b61f9 +
          '</strong>.</p><div class="senha-dialogo__destaque' +
          (_0x238491 ? " senha-dialogo__destaque--urgente" : "") +
          '">' +
          _0x4e17e5 +
          "</div><p>" +
          _0x3ac7e4 +
          "</p></div>";
      function _0x1fd5b9(_0x394a08, _0x1ea724) {
        _0x394a08.find(".senha-dialogo__erro").text(_0x1ea724).show();
        var _0x13bf3d = _0x394a08.closest(".ui-dialog-content");
        setTimeout(function () {
          (_0x13bf3d.dialog("option", "height", "auto"),
            _0x13bf3d.dialog("option", "position", "center"));
        }, 0x0);
      }
      function _0x46a951(_0x35b829, _0x59b8a8) {
        _0x59b8a8 = _0x59b8a8 || {};
        var _0x397d51 =
            _0x59b8a8.email !== undefined ? _0x59b8a8.email : _0x3b10b4,
          _0x4e1998 =
            _0x59b8a8.urlAlteracao ||
            "/sistema/funcoes/senha/alterar/?ajax=true",
          _0xc7dd9 = _0x35b829.find("p"),
          _0x2070e6 =
            '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="2.7" fill="currentColor"/></svg>',
          _0x4ac1f5 =
            '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z" fill="none" stroke="currentColor" stroke-width="2"/><path d="M4 4l16 16" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>';
        (_0xc7dd9.html(
          '<div class="senha-dialogo"><p class="senha-dialogo__titulo">Defina uma nova senha</p><p>Escolha uma nova senha para continuar.</p><p class="senha-dialogo__regras">Use de 8 a 20 caracteres, incluindo ao menos uma letra maiúscula, uma minúscula e um número. Não use a senha atual nem as três anteriores.</p><label for="novaSenhaAviso">Nova senha</label><div class="senha-dialogo__campo-senha"><input id="novaSenhaAviso" type="password" autocomplete="new-password"><button class="senha-dialogo__ver-senha" type="button" aria-label="Mostrar senha">' +
            _0x2070e6 +
            '</button></div><label for="confirmacaoSenhaAviso">Confirme a nova senha</label><div class="senha-dialogo__campo-senha"><input id="confirmacaoSenhaAviso" type="password" autocomplete="new-password"><button class="senha-dialogo__ver-senha" type="button" aria-label="Mostrar senha">' +
            _0x2070e6 +
            '</button></div><div class="senha-dialogo__erro"></div><p class="senha-dialogo__email"><label for="novoEmailAviso">E-mail para receber a confirmação</label><input id="novoEmailAviso" type="email" value="' +
            _0x397d51 +
            '" autocomplete="email" readonly></p></div>',
        ),
          _0xc7dd9.find(".senha-dialogo__ver-senha").click(function () {
            var _0x334093 = $(this),
              _0x525751 = _0x334093.siblings("input"),
              _0x39a214 = _0x525751[0x0].type === "password";
            ((_0x525751[0x0].type = _0x39a214 ? "text" : "password"),
              _0x334093
                .html(_0x39a214 ? _0x4ac1f5 : _0x2070e6)
                .attr(
                  "aria-label",
                  _0x39a214 ? "Ocultar senha" : "Mostrar senha",
                ));
          }),
          setTimeout(function () {
            (_0x35b829.dialog("option", "height", "auto"),
              _0x35b829.dialog("option", "position", "center"));
          }, 0x0),
          $.getJSON("/sistema/funcoes/senha/token/?ajax=true")
            .done(function (_0x40e865) {
              if (!_0x40e865 || !_0x40e865.code || !_0x40e865.csrf) {
                _0x1fd5b9(
                  _0xc7dd9,
                  "Não foi possível preparar a alteração da senha. Atualize a página e tente novamente.",
                );
                return;
              }
              var _0x13f142 = {
                "Salvar nova senha": function () {
                  var _0x223763 = $(this),
                    _0x498e9b = _0x223763.find("p");
                  (_0x498e9b.find(".senha-dialogo__erro").hide(),
                    _0x223763
                      .parent()
                      .find(".ui-dialog-buttonpane button")
                      .prop("disabled", true));
                  var _0x50d628 = dialogAlert(
                      "Aguarde",
                      '<div class="senha-dialogo"><div class="senha-dialogo__processando"><strong>Salvando sua senha...</strong><br>Enviando a confirmação por e-mail.</div></div>',
                    ),
                    _0xc74283 = $("#dc-" + _0x50d628);
                  _0xc74283.parent().addClass("senha-dialogo-modal");
                  var _0x2b309b = {
                    csrf: _0x40e865.csrf,
                    novaSenha: _0x498e9b.find("#novaSenhaAviso").val(),
                    confirmacaoNovaSenha: _0x498e9b
                      .find("#confirmacaoSenhaAviso")
                      .val(),
                    novoEmail: _0x498e9b.find("#novoEmailAviso").val(),
                  };
                  (_0x59b8a8.administrativo &&
                    ((_0x2b309b.C007_Id = _0x59b8a8.C007_Id),
                    (_0x2b309b.tokenAdministrativo =
                      _0x59b8a8.tokenAdministrativo)),
                    $.ajax({
                      url: _0x4e1998,
                      type: "POST",
                      dataType: "json",
                      data: _0x2b309b,
                    })
                      .done(function (_0x15916d) {
                        if (!_0x15916d || !_0x15916d.code) {
                          var _0x52d64e =
                            _0x15916d && _0x15916d.data
                              ? _0x15916d.data
                              : "Não foi possível alterar a senha.";
                          _0x1fd5b9(
                            _0x498e9b,
                            $.isArray(_0x52d64e)
                              ? _0x52d64e.join(" ")
                              : _0x52d64e,
                          );
                          return;
                        }
                        var _0xaeba9a = _0x15916d.emailEnviado
                          ? "<br>Enviamos a confirmação para " +
                            _0xa488e8(_0x15916d.email) +
                            "."
                          : "<br><strong>A senha foi alterada, mas não foi possível enviar o e-mail de confirmação.</strong>";
                        (_0x498e9b.html(
                          '<div class="senha-dialogo"><div class="senha-dialogo__sucesso"><strong>Senha alterada com sucesso.</strong><br>A próxima alteração será solicitada em ' +
                            _0x15916d.dataVencimento +
                            "." +
                            _0xaeba9a +
                            "</div></div>",
                        ),
                          _0x223763.dialog("option", "buttons", {
                            Continuar: function () {
                              window.location.reload();
                            },
                          }),
                          setTimeout(function () {
                            _0x223763.dialog("option", "position", "center");
                          }, 0x0));
                      })
                      .fail(function () {
                        _0x1fd5b9(
                          _0x498e9b,
                          "Não foi possível comunicar com o servidor. Tente novamente.",
                        );
                      })
                      .always(function () {
                        (_0xc74283.dialog("destroy").remove(),
                          _0x223763
                            .parent()
                            .find(".ui-dialog-buttonpane button")
                            .prop("disabled", false));
                      }));
                },
              };
              if (_0x59b8a8.administrativo || _0x59b8a8.podeFechar)
                _0x13f142.Fechar = function () {
                  $(this).dialog("destroy").remove();
                };
              _0x35b829.dialog("option", "buttons", _0x13f142);
            })
            .fail(function () {
              _0x1fd5b9(
                _0xc7dd9,
                "Não foi possível preparar a alteração da senha. Atualize a página e tente novamente.",
              );
            }));
      }
      function _0x3bd70e(_0x535ebd) {
        var _0x335793 = _0x535ebd.find("p");
        (_0x335793.html(
          '<div class="senha-dialogo"><p class="senha-dialogo__titulo">Alterar e-mail cadastrado</p><p>Informe o novo e-mail e confirme sua senha atual para salvar.</p><label for="senhaAtualEmail">Senha atual</label><input id="senhaAtualEmail" type="password" autocomplete="current-password"><label for="novoEmailAviso">Novo e-mail</label><input id="novoEmailAviso" type="email" autocomplete="email"><label for="confirmacaoEmailAviso">Confirme o novo e-mail</label><input id="confirmacaoEmailAviso" type="email" autocomplete="email"><div class="senha-dialogo__erro"></div></div>',
        ),
          setTimeout(function () {
            _0x535ebd.dialog("option", "position", "center");
          }, 0x0),
          $.getJSON("/sistema/funcoes/senha/token/?ajax=true")
            .done(function (_0x5b5802) {
              if (!_0x5b5802 || !_0x5b5802.code || !_0x5b5802.csrf) {
                _0x1fd5b9(
                  _0x335793,
                  "Não foi possível preparar a alteração do e-mail. Atualize a página e tente novamente.",
                );
                return;
              }
              _0x535ebd.dialog("option", "buttons", {
                "Salvar e-mail": function () {
                  var _0x165df7 = $(this),
                    _0x3133ec = _0x165df7.find("p");
                  (_0x3133ec.find(".senha-dialogo__erro").hide(),
                    $.ajax({
                      url: "/sistema/funcoes/senha/alterarEmail/?ajax=true",
                      type: "POST",
                      dataType: "json",
                      data: {
                        csrf: _0x5b5802.csrf,
                        senhaAtual: _0x3133ec.find("#senhaAtualEmail").val(),
                        novoEmail: _0x3133ec.find("#novoEmailAviso").val(),
                        confirmacaoEmail: _0x3133ec
                          .find("#confirmacaoEmailAviso")
                          .val(),
                      },
                    })
                      .done(function (_0x8d8e66) {
                        if (!_0x8d8e66 || !_0x8d8e66.code) {
                          var _0x1d49c8 =
                            _0x8d8e66 && _0x8d8e66.data
                              ? _0x8d8e66.data
                              : "Não foi possível alterar o e-mail.";
                          _0x1fd5b9(
                            _0x3133ec,
                            $.isArray(_0x1d49c8)
                              ? _0x1d49c8.join(" ")
                              : _0x1d49c8,
                          );
                          return;
                        }
                        (_0x3133ec.html(
                          '<div class="senha-dialogo"><div class="senha-dialogo__sucesso"><strong>E-mail atualizado com sucesso.</strong><br>O endereço cadastrado agora é ' +
                            $("<div>").text(_0x8d8e66.email).html() +
                            ".</div></div>",
                        ),
                          _0x165df7.dialog("option", "buttons", {
                            Continuar: function () {
                              window.location.reload();
                            },
                          }),
                          setTimeout(function () {
                            _0x165df7.dialog("option", "position", "center");
                          }, 0x0));
                      })
                      .fail(function () {
                        _0x1fd5b9(
                          _0x3133ec,
                          "Não foi possível comunicar com o servidor. Tente novamente.",
                        );
                      }));
                },
                Voltar: function () {
                  window.location.reload();
                },
              });
            })
            .fail(function () {
              _0x1fd5b9(
                _0x335793,
                "Não foi possível preparar a alteração do e-mail. Atualize a página e tente novamente.",
              );
            }));
      }
      ((window.hardnessAbrirDialogoSenha = function () {
        var _0x22663e = dialogConfirm(
            "Segurança da senha",
            "<p></p>",
            {},
            false,
          ),
          _0x4201ee = $("#dc-" + _0x22663e);
        (_0x4201ee.parent().addClass("senha-dialogo-modal"),
          _0x46a951(_0x4201ee, { podeFechar: true }));
      }),
        (window.hardnessAbrirDialogoSenhaAdministrativo = function (
          _0x31e1e0,
          _0xee1acc,
        ) {
          if (!_0x31e1e0 || !_0xee1acc) {
            dialogConfirm(
              "Erro",
              "Não foi possível identificar o usuário para alterar a senha.",
            );
            return;
          }
          $.getJSON(
            "/sistema/funcoes/senha/statusAdmin/?ajax=true&C007_Id=" +
              encodeURIComponent(_0x31e1e0) +
              "&token=" +
              encodeURIComponent(_0xee1acc),
          )
            .done(function (_0x58b51f) {
              if (!_0x58b51f || !_0x58b51f.code) {
                dialogConfirm(
                  "Erro",
                  _0x58b51f && _0x58b51f.data
                    ? _0x58b51f.data
                    : "Não foi possível preparar a alteração da senha.",
                );
                return;
              }
              var _0x1593e3 = dialogConfirm(
                  "Segurança da senha",
                  "<p></p>",
                  {},
                  false,
                ),
                _0xb9238e = $("#dc-" + _0x1593e3);
              (_0xb9238e.parent().addClass("senha-dialogo-modal"),
                _0x46a951(_0xb9238e, {
                  email: $("<div>")
                    .text(_0x58b51f.email || "")
                    .html(),
                  administrativo: true,
                  C007_Id: _0x31e1e0,
                  tokenAdministrativo: _0xee1acc,
                  urlAlteracao:
                    "/sistema/funcoes/senha/alterarAdmin/?ajax=true",
                }));
            })
            .fail(function () {
              dialogConfirm(
                "Erro",
                "Não foi possível comunicar com o servidor. Tente novamente.",
              );
            });
        }));
      if (!_0x23f33c.mostrar) return;
      var _0x3bfbf0 = {
        "Alterar senha agora": function () {
          _0x46a951($(this));
        },
      };
      if (!_0x23f33c.obrigatoria)
        _0x3bfbf0["Lembrar depois"] = function () {
          $(this).dialog("destroy").remove();
        };
      var _0xfb07a7 = dialogConfirm(
          "Sua senha precisa de atenção",
          _0x204715,
          _0x3bfbf0,
          false,
        ),
        _0xfd8970 = $("#dc-" + _0xfb07a7);
      _0xfd8970.parent().addClass("senha-dialogo-modal");
      if (_0x23f33c.obrigatoria)
        _0xfd8970.parent().addClass("senha-dialogo-modal--obrigatoria");
      _0x23f33c.obrigatoria &&
        (_0xfd8970.dialog("option", "closeOnEscape", false),
        _0xfd8970.on("dialogbeforeclose", function (_0x34bfe1) {
          _0x34bfe1.preventDefault();
        }));
    },
  );
});
function opcoesZoom() {
  $(".zoomButton")
    .unbind()
    .bind("click", function (_0x3bb5ec) {
      if (!fontSizeOption) {
        var _0x413e5c = $(this);
        _0x413e5c.css("z-index", 0x1869f);
        var _0x5f2622 = $("<ul></ul>")
            .addClass("dropdown-menu pull-left zoomOptions")
            .attr("role", "menu")
            .css({
              display: "block",
              top: "32px",
              "margin-left": "-55px",
              position: "absolute",
              "min-width": "0px",
              width: "180px",
            }),
          _0x5156c9 = parseInt($("body").css("font-size")),
          _0x38bb8d = "principal";
        $('link[rel~="stylesheet"]').each(function () {
          var _0x4988f0 = ($(this).attr("href") || "").split(/[?#]/)[0x0],
            _0xacabdb = _0x4988f0.match(
              /\/static\/css\/tema-(2021|azul-claro-contraste|claro|escuro)\.css$/,
            );
          _0xacabdb &&
            (_0x38bb8d =
              _0xacabdb[0x1] == "2021" ? "azul-claro" : _0xacabdb[0x1]);
        });
        var _0x3ed1a2 = $('<div id="layout">')
          .appendTo("body")
          .css("font-family");
        ($(
          '<li style="color: var(--letra-dropdown-menu); text-align: center; border-bottom: 1px solid #e6e6e6; padding: 4px; margin-bottom: 4px;">Tamanho da fonte</li>',
        ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTamanhoFonte(12);" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (_0x5156c9 == 0xc ? " active" : "") +
              '">Pequena</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTamanhoFonte(13);" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-M zoomButtonOption' +
              (_0x5156c9 == 0xd ? " active" : "") +
              '">Média</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTamanhoFonte(14);" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-G zoomButtonOption' +
              (_0x5156c9 == 0xe ? " active" : "") +
              '">Grande</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li style="color: var(--letra-dropdown-menu); text-align: center; border-bottom: 1px solid #e6e6e6; border-top: 1px solid #e6e6e6; padding: 4px; padding-top: 8px; margin-bottom: 4px; margin-top: 4px;">Tema de cores</li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTema(\'principal\');"            style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (!_0x38bb8d || _0x38bb8d == "principal" ? " active" : "") +
              '">Padrão</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTema(\'2021\');"                 style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (_0x38bb8d == "azul-claro" ? " active" : "") +
              '">Azul claro</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTema(\'azul-claro-contraste\');" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (_0x38bb8d == "azul-claro-contraste" ? " active" : "") +
              '">Azul claro contraste</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTema(\'claro\');"                style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (_0x38bb8d == "claro" ? " active" : "") +
              '">Branco</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarTema(\'escuro\');"               style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (_0x38bb8d == "escuro" ? " active" : "") +
              '">Preto</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li style="color: var(--letra-dropdown-menu); text-align: center; border-bottom: 1px solid #e6e6e6; border-top: 1px solid #e6e6e6; padding: 4px; padding-top: 8px; margin-bottom: 4px; margin-top: 4px;">Layout</li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarLayout(\'padrao\');" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (!_0x3ed1a2 || _0x3ed1a2 == "padrao" ? " active" : "") +
              '">Padrão</button></li>',
          ).appendTo(_0x5f2622),
          $(
            '<li role="presentation" onclick="alterarLayout(\'expandido\');" style="text-align: left; padding: 0 10px 0 6px;"><button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only zoomButtonOption-P zoomButtonOption' +
              (!_0x3ed1a2 || _0x3ed1a2 == "expandido" ? " active" : "") +
              '">Expandido</button></li>',
          ).appendTo(_0x5f2622),
          (fontSizeOption = true),
          _0x5f2622.appendTo(_0x413e5c),
          $("body")
            .unbind("click.zoomButton")
            .bind("click.zoomButton", function (_0x18d760) {
              !$(_0x18d760.target).hasClass("zoomButton") &&
                !$(_0x18d760.target).hasClass("zoom") &&
                ($(".zoomOptions").hide(),
                $("#topo .zoomButton").css("z-index", 0x1),
                (fontSizeOption = false),
                $("body").unbind("click.zoomButton"));
            }),
          $('<div class="arrow"></div>').appendTo(_0x5f2622));
      } else
        ($(".zoomOptions").hide(),
          $("#topo .zoomButton").css("z-index", 0x1),
          (fontSizeOption = false),
          $("body").unbind("click.zoomButton"));
    });
}
function abrirJanela(
  _0x3dbb2a,
  _0x559797,
  _0x2f4516,
  _0x423b0f,
  _0x5566ba,
  _0x3c5ee6,
  _0x721db9,
  _0x4c3893,
  _0x5872cf,
  _0x21510a,
  _0x561bdf,
  _0x14b3d2,
) {
  _0x5872cf != "forcarAuto" &&
    isset(g.tamanhosJanelas) &&
    isset(g.tamanhosJanelas[_0x721db9]) &&
    (typeof _0x5872cf === "string"
      ? (_0x21510a = [0x258, 0x190])
      : (_0x21510a = _0x5872cf),
    (_0x5872cf = g.tamanhosJanelas[_0x721db9]));
  _0x5872cf == "forcarAuto" && (_0x5872cf = "auto");
  if (_0x3dbb2a.ctrlKey)
    return (
      nova_janela(
        _0x721db9 + "?oneWindow=true" + _0x4c3893,
        0x0,
        0x0,
        0x3f2,
        0x226,
        "no",
        "no",
        "no",
        "no",
        "yes",
        "yes",
        0x0,
      ),
      false
    );
  fixDesktopSize();
  var _0x4c3893 = typeof _0x4c3893 == "undefined" ? "" : _0x4c3893,
    _0x5872cf = typeof _0x5872cf == "undefined" ? "auto" : _0x5872cf,
    _0x21510a = typeof _0x21510a == "undefined" ? false : _0x21510a,
    _0x2f4516 = typeof _0x2f4516 == "undefined" ? "" : _0x2f4516,
    _0x561bdf = typeof _0x561bdf == "undefined" ? "center" : _0x561bdf,
    _0x14b3d2 = typeof _0x14b3d2 == "undefined" ? true : _0x14b3d2;
  !isset(windows[_0x559797]) && (windows[_0x559797] = {});
  if (isempty(windows[_0x559797])) {
  } else {
    var _0x92c36d = false,
      _0x255b42 = false,
      _0x3877f7 = false;
    for (var _0x1294ea in windows) {
      if (_0x559797 == _0x1294ea && !isempty(windows[_0x1294ea])) {
        _0x255b42 = _0x1294ea;
        for (var _0x1001df in windows[_0x559797]) {
          ((_0x3877f7 = _0x1001df),
            _0x1001df == _0x423b0f && (_0x92c36d = true));
        }
      }
    }
    if (_0x92c36d)
      return ($("#toolbar-btn-" + _0x255b42).trigger("click"), false);
    else {
    }
    refreshBtns();
  }
  return (
    janelasDesativar(),
    (windows[_0x559797][_0x423b0f] = {
      open: true,
      active: true,
      tituloBtn: _0x5566ba,
    }),
    refreshBtns(),
    setTimeout(function () {
      var _0x5480ce,
        _0x5a05b0 = $(_0x3dbb2a.currentTarget);
      !(
        _0x5a05b0 &&
        _0x5a05b0.get(0x0) &&
        _0x5a05b0.get(0x0).nodeName &&
        _0x5a05b0.is("tr")
      ) && (_0x5a05b0 = $(_0x3dbb2a.target));
      _0x4c3893.indexOf("naoAtualizarLinha") === -0x1 &&
        _0x5a05b0 &&
        _0x5a05b0.get(0x0) &&
        _0x5a05b0.get(0x0).nodeName &&
        _0x5a05b0.is("tr") &&
        _0x5a05b0.closest("table").hasClass("grid") &&
        (_0x5480ce = function () {
          divRefresh(
            _0x2f4516,
            true,
            false,
            false,
            _0x5a05b0.attr("id"),
            false,
          );
        });
      novoDialog(
        _0x559797,
        _0x423b0f,
        _0x5566ba,
        _0x3c5ee6,
        _0x5872cf,
        _0x21510a,
        _0x561bdf,
        _0x14b3d2,
        _0x721db9,
        _0x5480ce,
      );
      var _0x1420a7 = $("#" + _0x2f4516).data("divIdChain");
      $("#" + _0x423b0f).data({
        janelaIdRoot: _0x559797,
        janelaIdParent: _0x2f4516,
        janelaIdParentChain: _0x1420a7,
        janelaId: _0x423b0f,
        janelaHref: _0x721db9,
        janelaVars: _0x4c3893,
        janelaTituloBtn: _0x5566ba,
        janelaTituloWin: _0x3c5ee6,
        janelaPermissao: abrirJanelaPermissao,
      });
      if (!isempty(_0x721db9)) {
        $("#" + _0x423b0f).showLoading();
        var _0x41f57b = $.ajax({
          url: _0x721db9,
          dataType: "html",
          data:
            "ajax=true&divId=" +
            encodeURIComponent(_0x423b0f) +
            "&divIdParent=" +
            encodeURIComponent(_0x2f4516) +
            "&divIdParentChain=" +
            encodeURIComponent(_0x1420a7) +
            "&divIdRoot=" +
            encodeURIComponent(_0x559797) +
            _0x4c3893,
          type: "post",
          success: function (_0x2b060c) {
            ($("#" + _0x423b0f).hideLoading(),
              $("#" + _0x423b0f).html(_0x2b060c),
              reloadUI(_0x423b0f));
          },
        });
        (typeof requisicoesAjax["{$value[0]}"] == "undefined" &&
          (requisicoesAjax[_0x423b0f] = []),
          requisicoesAjax[_0x423b0f].push(_0x41f57b));
      }
    }, 0x64),
    true
  );
}
function novoDialog(
  _0x369cfe,
  _0x287e3f,
  _0x310b7d,
  _0x2960ab,
  _0x4831dc,
  _0x5c11bf,
  _0x26704e,
  _0x1ec0ca,
  _0x3fc217,
  _0x2aeeb4,
) {
  var _0x4831dc = typeof _0x4831dc == "undefined" ? "auto" : _0x4831dc,
    _0x5c11bf =
      typeof _0x5c11bf == "undefined" || isempty(_0x5c11bf) ? false : _0x5c11bf,
    _0x26704e = typeof _0x26704e == "undefined" ? "center" : _0x26704e,
    _0x1ec0ca = typeof _0x1ec0ca == "undefined" ? true : _0x1ec0ca,
    _0x3fc217 = typeof _0x3fc217 == "undefined" ? true : _0x3fc217;
  if ($("#" + _0x287e3f).size() != 0x0) returnfalse;
  _0x4831dc == "auto" &&
    _0x26704e == "center" &&
    $(".ui-dialog").size() == 0x0 &&
    ((_0x4831dc = "full"), (_0x26704e = "topleft"), (_0x1ec0ca = false));
  $("<div>")
    .attr({ id: _0x287e3f, title: _0x2960ab })
    .css({ display: "none", position: "relative" })
    .appendTo("#desktop");
  var _0x5165d0 = $("#" + _0x287e3f),
    _0x2b918b = Math.floor(desktopWidth - 0x24),
    _0x175fb5 = Math.floor(desktopHeight - 0x24);
  typeof _0x4831dc == "object" &&
    ((_0x2b918b = _0x4831dc[0x0]), (_0x175fb5 = _0x4831dc[0x1]));
  if (_0x4831dc == "full" || oneWindow)
    (_0x1ec0ca
      ? ((_0x2b918b =
          desktopWidth -
          (desktopLeft + desktopRight) -
          dialogBorder * 0x4 +
          0x2),
        (_0x175fb5 = desktopHeight - 0x5))
      : ((_0x2b918b =
          desktopWidth - (desktopLeft + desktopRight) - dialogBorder * 0x4),
        (_0x175fb5 = desktopHeight - 0xc)),
      (_0x319b6f = [desktopLeft, desktopUpFull]));
  else {
    if (_0x26704e == "topleft") var _0x319b6f = [desktopLeft, desktopUp];
    else {
      if (_0x26704e == "center")
        var _0x319b6f = { my: "center", at: "center", of: $("#desktop") };
      else {
        if (_0x26704e == "auto")
          var _0x319b6f = [
            mt_rand(desktopLeft, desktopWidth - _0x2b918b - desktopRight),
            mt_rand(
              desktopUp,
              desktopHeight - _0x175fb5 - dialogBorder * 0x6 + desktopUp,
            ),
          ];
      }
    }
  }
  if (typeof _0x5c11bf == "object")
    var _0x3ffbed = _0x5c11bf[0x0],
      _0x5090b1 = _0x5c11bf[0x1];
  else
    var _0x3ffbed = _0x2b918b,
      _0x5090b1 = _0x175fb5;
  (_0x5165d0.data("minWidth", _0x3ffbed),
    _0x5165d0.data("minHeight", _0x5090b1));
  _0x4831dc == "auto" && _0x5165d0.data("forceFull", true);
  var _0x29c406 = false,
    _0x13ce01 = true;
  _0x369cfe == "telaLogin" && ((_0x29c406 = true), (_0x13ce01 = false));
  _0x5165d0.dialog({
    modal: _0x29c406,
    width: _0x2b918b,
    minWidth: 0x64,
    height: _0x175fb5,
    minHeight: 0x64,
    position: _0x319b6f,
    autoOpen: true,
    closeOnEscape: _0x13ce01,
    dragStart: function (_0x35173b, _0x12cf2c) {
      ($(".colunaAcoes").remove(), $(".list-options-form").remove());
    },
    dragStop: function (_0x5b1518, _0x404b65) {
      var _0x478ea7 = $(this);
      _0x404b65.position.left < desktopLeft &&
        (_0x478ea7.dialog("option", "position", [
          desktopLeft,
          _0x404b65.position.top,
        ]),
        (_0x404b65.position.left = desktopLeft));
      _0x404b65.position.top < desktopUp &&
        (_0x478ea7.dialog("option", "position", [
          _0x404b65.position.left,
          desktopUp,
        ]),
        (_0x404b65.position.top = desktopUp));
      var _0x57d3d3 = desktopWidth - _0x478ea7.parent().outerWidth(),
        _0x21350f = desktopHeight - _0x478ea7.parent().outerHeight() + 0x3;
      (_0x404b65.position.left > _0x57d3d3 - (desktopRight + desktopLeft) &&
        (_0x478ea7.dialog("option", "position", [
          _0x57d3d3 - (desktopRight + desktopLeft),
          _0x404b65.position.top,
        ]),
        (_0x404b65.position.left = _0x57d3d3 - (desktopRight + desktopLeft))),
        _0x404b65.position.top > _0x21350f + desktopUp - dialogBorder * 0x2 &&
          (_0x478ea7.dialog("option", "position", [
            _0x404b65.position.left,
            _0x21350f + desktopUp - dialogBorder * 0x2,
          ]),
          (_0x404b65.position.top =
            _0x21350f + desktopUp - dialogBorder * 0x2)));
    },
    resizeStart: function (_0xd5a090, _0x58bc8f) {
      ($(".colunaAcoes").remove(), $(".list-options-form").remove());
    },
    resizeStop: function (_0x318bb1, _0x48f431) {
      var _0x4bcc55 = $(this);
      (_0x4bcc55.data(
        "originalHeight",
        getComputedStyle(_0x4bcc55[0x0])
          .getPropertyValue("height")
          .replace("px", ""),
      ),
        _0x4bcc55.data(
          "originalWidth",
          getComputedStyle(_0x4bcc55[0x0])
            .getPropertyValue("width")
            .replace("px", ""),
        ),
        _0x48f431.size.width >
          desktopWidth -
            _0x48f431.position.left -
            (desktopLeft + desktopRight) -
            dialogBorder * 0x2 &&
          _0x4bcc55.dialog(
            "option",
            "width",
            desktopWidth -
              _0x48f431.position.left -
              (desktopLeft + desktopRight) -
              dialogBorder * 0x2,
          ),
        _0x48f431.size.height >
          desktopHeight -
            _0x48f431.position.top +
            desktopUp -
            dialogBorder * 0x6 &&
          _0x4bcc55.dialog(
            "option",
            "height",
            desktopHeight -
              _0x48f431.position.top +
              desktopUp -
              dialogBorder * 0x6,
          ),
        _0x4bcc55
          .find(".content:visible")
          .each(function (_0x56cd8a, _0x200105) {
            (corrigeConteudo($(this).attr("id")),
              corrigeGrid($(this).attr("id")));
          }),
        (g.tamanhosJanelas[_0x4bcc55.data("janelaHref")] = [
          _0x4bcc55.dialog("option", "width"),
          _0x4bcc55.dialog("option", "height"),
        ]),
        $.ajax({
          url: "/sistema/funcoes/tamanhosJanelas/salvar/",
          dataType: "html",
          data:
            "ajax=true&json=" +
            encodeURIComponent(JSON.stringify(g.tamanhosJanelas)),
          type: "post",
        }));
    },
    beforeClose: function (_0x553251, _0x30a281) {
      if (_0x553251.keyCode == 0x1b) {
        if (
          $(":focus").hasClass("input-editar-campo") ||
          ($(":focus").is("select") &&
            $(":focus").closest("tr.linhaSelecionada").size() > 0x0)
        )
          returnfalse;
        var _0x1f01b4 = $(this),
          _0x324ef2 = $(this).data("janelaHref");
        if (
          isset(g.janelasIgnorarESC[_0x324ef2]) &&
          g.janelasIgnorarESC[_0x324ef2] == 0x1
        ) {
          var _0x2c2da2 = {
            Sim: function () {
              ($(this).dialog("destroy").remove(),
                _0x1f01b4.closest(".ui-dialog-content").dialog("close"));
            },
            Não: function () {
              $(this).dialog("destroy").remove();
              var _0x57f91b = false;
              _0x1f01b4.find("label:visible").each(function () {
                if (_0x57f91b) returnfalse;
                var _0x536260 = $(this).find(
                  "input:visible,textarea:visible,select:visible",
                );
                _0x536260.size() != 0x0 &&
                  ((_0x57f91b = true),
                  _0x536260.closest(".ui-tabs-panel").data("divIdRoot") !=
                    "B.I." &&
                    !_0x536260.hasClass("campo_input_data") &&
                    _0x536260.focus());
              });
            },
          };
          return (
            dialogConfirm("Atenção", "Deseja fechar esta janela? ", _0x2c2da2),
            false
          );
        }
      }
      return (
        $(".loading-indicator").remove(),
        $(".loading-indicator-overlay").remove(),
        abortarRequisicoesAjax($(this)),
        true
      );
    },
    close: function (_0x2f7f38, _0x177d03) {
      ($(".colunaAcoes").remove(), $(".list-options-form").remove());
      var _0x13d237 = $(this);
      $(".popover").remove();
      if (_0x13d237.data("janelaIdRoot")) {
        currentFocus = null;
        var _0x187052 = _0x13d237.data("janelaIdRoot"),
          _0x9d52b7 = _0x13d237.data("janelaId");
        (_0x13d237.hide(),
          setTimeout(function () {
            (!closeLastBtn && janelasDesativar(),
              (windows[_0x187052][_0x9d52b7].remove = true),
              refreshBtns());
          }, 0x64),
          setTimeout(function () {
            _0x13d237.remove();
            var _0x54c131 = $(".ui-dialog:visible:last > .ui-dialog-content"),
              _0x405d89 = _0x54c131.data("janelaId");
            if (typeof _0x405d89 != "undefined") {
              var _0x2202b4 = _0x54c131.data("janelaIdRoot");
              ($("#w-overlay-" + _0x405d89).css({
                "z-index": $("#" + _0x405d89)
                  .parent()
                  .css("z-index"),
              }),
                janelasDesativar());
              for (var _0x405d89 in windows[_0x2202b4]) {
                windows[_0x2202b4][_0x405d89].open = true;
              }
              ((windows[_0x2202b4][_0x405d89].active = true),
                _0x54c131.hasClass("toolbar-btn-active") &&
                  janelasAtivarUltima(),
                refreshBtns(),
                refreshWins(),
                janelasHistorico(_0x405d89));
            }
          }, 0xc8));
        if (!isempty(_0x13d237.data("janelaIdParent"))) {
          $(
            "#w-overlay-" +
              $("#" + _0x13d237.data("janelaIdParent"))
                .closest(".ui-dialog-content")
                .attr("id"),
          ).remove();
          var _0x253f57 = false;
          $("#" + _0x13d237.data("janelaIdParent"))
            .find(".grid:visible:last .linhaSelecionada")
            .size() == 0x0 &&
            $("#" + _0x13d237.data("janelaIdParent"))
              .find("label:visible")
              .each(function () {
                if (_0x253f57) returnfalse;
                var _0x4e48a9 = $(this).find(
                  "input:visible:not([disabled]), textarea:visible:not([disabled])",
                );
                _0x4e48a9.size() != 0x0 &&
                  _0x4e48a9.closest(".ui-tabs-panel").data("divIdRoot") !=
                    "B.I." &&
                  !_0x4e48a9.hasClass("campo_input_data") &&
                  ((_0x253f57 = true), _0x4e48a9.focus());
              });
        }
      }
      typeof _0x2aeeb4 === "function" && _0x2aeeb4();
    },
    focus: function (_0xb5fdcd, _0x4555d0) {
      var _0x3b2316 = $(this);
      if (_0x3b2316.data("janelaIdRoot")) {
        var _0xe14b38 = _0x3b2316.data("janelaId"),
          _0x443c37 = _0x3b2316.data("janelaIdRoot");
        ($("#w-overlay-" + _0xe14b38).css({
          "z-index": $("#" + _0xe14b38)
            .parent()
            .css("z-index"),
        }),
          janelasDesativar());
        for (var _0xe14b38 in windows[_0x443c37]) {
          windows[_0x443c37][_0xe14b38].open = true;
        }
        ((windows[_0x443c37][_0xe14b38].active = true),
          _0x3b2316.hasClass("toolbar-btn-active") && janelasAtivarUltima(),
          refreshBtns(),
          refreshWins(),
          janelasHistorico(_0xe14b38));
      }
    },
    open: function (_0xf69a46, _0x928a1e) {
      refreshWins();
      if ($(this).closest(".ui-dialog").is($(".ui-dialog:first"))) returnfalse;
      var _0x1fe6c0 = $(this).data("dialog").options,
        _0x17b743 = _0x1fe6c0.width,
        _0x3bfa7d = _0x1fe6c0.height;
      (_0x17b743 > desktopWidth || _0x3bfa7d > desktopHeight) &&
        (_0x5165d0.data("janelaHref", _0x3fc217),
        maximizeDialog($(this).closest(".ui-dialog"), true));
    },
  });
  oneWindow &&
    _0x5165d0.attr("id") == "oneWindow" &&
    (_0x5165d0.dialog("option", "draggable", false),
    _0x5165d0.dialog("option", "resizable", false),
    _0x5165d0.dialog("option", "closeOnEscape", false),
    _0x5165d0
      .parent()
      .css({ border: "none", "box-shadow": "none" })
      .removeClass("ui-corner-all")
      .width(_0x5165d0.parent().width() + 0x14)
      .find(".ui-dialog-titlebar")
      .remove(),
    _0x5165d0.css({ "padding-top": "1px" }).height(_0x5165d0.height() + 0x21));
  if (!_0x1ec0ca) {
    (_0x5165d0.dialog("option", "draggable", false),
      _0x5165d0.dialog("option", "resizable", false),
      _0x5165d0.dialog("option", "closeOnEscape", false),
      _0x5165d0
        .parent()
        .addClass("noBorder")
        .removeClass("ui-corner-all")
        .width(_0x5165d0.parent().width() + 0x8 - spaceBtw * 0x2)
        .find(".ui-dialog-titlebar")
        .remove());
    var _0x157c68 = parseInt($("body").css("font-size")),
      _0x23b425 = 0x2c;
    (_0x157c68 == 0xd && (_0x23b425 = 0x31),
      _0x157c68 == 0xe && (_0x23b425 = 0x33),
      _0x5165d0.height(_0x5165d0.height() + _0x23b425 - spaceBtw * 0x2),
      _0x5165d0.parent().css("padding", spaceBtw + "px"));
  }
  return (
    _0x369cfe == "telaLogin" &&
      (_0x5165d0.dialog("option", "draggable", false),
      _0x5165d0.dialog("option", "resizable", false),
      _0x5165d0
        .parent()
        .removeClass("ui-corner-all")
        .find(".ui-dialog-titlebar a")
        .remove()),
    janelasHistorico(_0x287e3f),
    true
  );
}
function refreshBtns() {
  for (var _0x5936c5 in windows) {
    var _0x6e2be1 = $("#toolbar-btn-" + _0x5936c5);
    if (_0x6e2be1.size())
      for (var _0x45d4aa in windows[_0x5936c5]) {
        if (windows[_0x5936c5][_0x45d4aa].remove) {
          delete windows[_0x5936c5][_0x45d4aa];
          if (countObj(windows[_0x5936c5]) == 0x0)
            (_0x6e2be1.remove(),
              $("#toolbar-sep-" + _0x5936c5).remove(),
              janelasAtivarUltima(),
              refreshBtns());
          else
            for (var _0xb3b6b0 in windows[_0x5936c5]) {
              ((windows[_0x5936c5][_0xb3b6b0].active = true),
                janelasHistorico(_0x45d4aa),
                $("#" + _0xb3b6b0).dialog("moveToTop"));
            }
          refreshWins();
        }
        $("#toolbar > .toolbar-btn").size() == 0x0 &&
          ($("#toolbar").hide(), (windowsHistory = []));
      }
    else
      for (var _0x45d4aa in windows[_0x5936c5]) {
        var _0x118b05 = $("<div>")
          .attr({ id: "toolbar-btn-" + _0x5936c5 })
          .addClass("toolbar-btn")
          .hover(
            function () {
              var _0x5c829c = $(this);
              (_0x5c829c.addClass("toolbar-btn-hover"),
                _0x5c829c
                  .find(".toolbar-btn-text")
                  .addClass("toolbar-btn-text-hover"));
            },
            function () {
              var _0xb33e0a = $(this);
              (_0xb33e0a.removeClass("toolbar-btn-hover"),
                _0xb33e0a
                  .find(".toolbar-btn-text")
                  .removeClass("toolbar-btn-text-hover"));
            },
          )
          .bind("click", function (_0x566951) {
            var _0x4645aa = $(this),
              _0x1c3250 = _0x4645aa.attr("id").replace(/^toolbar\-btn\-/, ""),
              _0x5750ee = false;
            for (var _0x3ceffb in windows[_0x1c3250]) {
              _0x4645aa.hasClass("toolbar-btn-active")
                ? ((windows[_0x1c3250][_0x3ceffb].open = false),
                  (windows[_0x1c3250][_0x3ceffb].active = false),
                  $("#" + _0x3ceffb)
                    .parent()
                    .hide())
                : ((windows[_0x1c3250][_0x3ceffb].open = true),
                  (windows[_0x1c3250][_0x3ceffb].active = true),
                  $("#" + _0x3ceffb)
                    .parent()
                    .show(),
                  (_0x5750ee = _0x3ceffb));
            }
            (_0x5750ee &&
              ($("#" + _0x5750ee).dialog("moveToTop"),
              janelasHistorico(_0x3ceffb)),
              _0x4645aa.hasClass("toolbar-btn-active") && janelasAtivarUltima(),
              refreshBtns(),
              refreshWins());
          });
        $("<div>")
          .addClass("toolbar-btn-text")
          .text(windows[_0x5936c5][_0x45d4aa].tituloBtn)
          .appendTo(_0x118b05);
        var _0x5bbf3b = $("<div>")
          .attr({ id: "toolbar-sep-" + _0x5936c5 })
          .addClass("toolbar-sep");
        (_0x118b05.appendTo("#toolbar"), _0x5bbf3b.appendTo("#toolbar"));
        var _0x6e2be1 = $("#toolbar-btn-" + _0x5936c5);
        $("#toolbar > .toolbar-btn").size() == 0x1 &&
          ($("#toolbar").show(), _0x6e2be1.addClass("ui-corner-tl"));
        break;
      }
    for (var _0x45d4aa in windows[_0x5936c5]) {
      (windows[_0x5936c5][_0x45d4aa].remove &&
        delete windows[_0x5936c5][_0x45d4aa],
        isset(windows[_0x5936c5][_0x45d4aa]) &&
        windows[_0x5936c5][_0x45d4aa].active
          ? (!_0x6e2be1.hasClass("toolbar-btn-active") &&
              _0x6e2be1.addClass("toolbar-btn-active"),
            !_0x6e2be1
              .find(".toolbar-btn-text")
              .hasClass("toolbar-btn-text-active") &&
              _0x6e2be1
                .find(".toolbar-btn-text")
                .addClass("toolbar-btn-text-active"))
          : (_0x6e2be1.removeClass("toolbar-btn-active"),
            _0x6e2be1
              .find(".toolbar-btn-text")
              .removeClass("toolbar-btn-text-active")));
    }
  }
}
function refreshWins() {
  var _0x5a8292, _0x586aea;
  for (var _0x2252ea in windows) {
    for (var _0x2f1238 in windows[_0x2252ea]) {
      windows[_0x2252ea][_0x2f1238].active &&
        ((_0x5a8292 = _0x2252ea), (_0x586aea = _0x2f1238));
    }
  }
  for (var _0x2252ea in windows) {
    var _0x39fc42 = 0x0,
      _0x3e3c03 = 0x0;
    for (var _0x2f1238 in windows[_0x2252ea]) {
      _0x3e3c03++;
    }
    for (var _0x2f1238 in windows[_0x2252ea]) {
      var _0x35d1eb = $("#w-overlay-" + _0x2f1238),
        _0x4126ba = $("#" + _0x2f1238),
        _0x200cdb = $("#" + _0x2f1238).parent();
      if (_0x2f1238 == _0x586aea)
        (_0x35d1eb.remove(),
          _0x200cdb.css({ opacity: "1" }),
          _0x200cdb.removeClass("ui-widget-content-gray"),
          _0x200cdb
            .find(".ui-dialog-titlebar")
            .removeClass("ui-widget-header-gray"));
      else {
        if (_0x2252ea == _0x5a8292) {
          if (!_0x35d1eb.size()) {
            var _0x38af38 = _0x200cdb.position();
            $("<div>")
              .css({
                position: "fixed",
                width: _0x200cdb.outerWidth() + "px",
                height: _0x200cdb.outerHeight() + "px",
                top: _0x38af38.top,
                left: _0x38af38.left,
                "z-index": _0x200cdb.css("z-index"),
                background: "black",
                opacity: "0.35",
                "border-radius": _0x200cdb.css("border-radius"),
              })
              .attr("id", "w-overlay-" + _0x2f1238)
              .addClass("w-overlay ui-dialog-overlay")
              .bind("click", function (_0x1ed259) {
                var _0x1a6bd8 = $(this)
                    .attr("id")
                    .replace(/^w\-overlay\-/, ""),
                  _0x2b9dc2 = $("#" + _0x1a6bd8).data("janelaIdRoot");
                for (var _0x1a6bd8 in windows[_0x2b9dc2]) {
                }
                $("#" + _0x1a6bd8).dialog("moveToTop");
              })
              .appendTo("body");
          }
          (_0x200cdb.removeClass("ui-widget-content-gray"),
            _0x200cdb
              .find(".ui-dialog-titlebar")
              .removeClass("ui-widget-header-gray"));
        } else
          closeLastBtn
            ? (_0x35d1eb.remove(), _0x4126ba.dialog("close"))
            : (_0x39fc42++,
              _0x39fc42 == _0x3e3c03 && _0x35d1eb.remove(),
              _0x200cdb.css({ opacity: "1" }),
              _0x200cdb.addClass("ui-widget-content-gray"),
              _0x200cdb
                .find(".ui-dialog-titlebar")
                .addClass("ui-widget-header-gray"));
      }
    }
  }
  fixZindex();
}
function fixZindex() {
  var _0x30072d = null,
    _0x25f44a = null;
  for (var _0xa6374 in windows) {
    for (var _0x227f60 in windows[_0xa6374]) {
      windows[_0xa6374][_0x227f60].active &&
        ((_0x30072d = _0x227f60), (_0x25f44a = _0xa6374));
    }
  }
  for (var _0xa6374 in windows) {
    if (_0xa6374 == _0x25f44a) continue;
    for (var _0x227f60 in windows[_0xa6374]) {
      (zIndex++,
        $("#" + _0x227f60)
          .parent()
          .css({ "z-index": zIndex }),
        $("#w-overlay-" + _0x227f60).css({ "z-index": zIndex }));
    }
  }
  for (var _0x227f60 in windows[_0x25f44a]) {
    (zIndex++,
      $("#" + _0x227f60)
        .parent()
        .css({ "z-index": zIndex }),
      $("#w-overlay-" + _0x227f60).css({ "z-index": zIndex }));
  }
}
function janelasDesativar() {
  for (var _0x579df8 in windows) {
    for (var _0x346fa0 in windows[_0x579df8]) {
      windows[_0x579df8][_0x346fa0].active = false;
    }
  }
}
function janelasHistorico(_0xcd7fb0) {
  var _0x3e84c0 = windowsHistory.length;
  _0x3e84c0 == 0x0
    ? (windowsHistory[_0x3e84c0] = _0xcd7fb0)
    : windowsHistory[_0x3e84c0 - 0x1] != _0xcd7fb0 &&
      (windowsHistory[_0x3e84c0] = _0xcd7fb0);
}
function janelasAtivarUltima() {
  if ($("#toolbar > .toolbar-btn").size() > 0x0) {
    var _0x30bc3f = false;
    for (i = windowsHistory.length - 0x1; i >= 0x0; i--) {
      var _0x45939a = windowsHistory[i];
      for (var _0x2e38d7 in windows) {
        for (var _0x3f0eef in windows[_0x2e38d7]) {
          _0x3f0eef == _0x45939a &&
            !isset(windows[_0x2e38d7][_0x3f0eef].remove) &&
            windows[_0x2e38d7][_0x3f0eef].open &&
            ((windows[_0x2e38d7][_0x3f0eef].active = true), (_0x30bc3f = true));
        }
      }
      if (_0x30bc3f) break;
    }
  }
}
function fecharJanela(_0x1e8f6d) {
  $("#" + _0x1e8f6d).hasClass("ui-dialog-content")
    ? oneWindow
      ? $("#" + _0x1e8f6d).attr("id") != "oneWindow" &&
        $("#" + _0x1e8f6d).dialog("close")
      : $("#" + _0x1e8f6d).dialog("close")
    : oneWindow
      ? $("#" + _0x1e8f6d)
          .closest(".ui-dialog-content")
          .attr("id") != "oneWindow" &&
        $("#" + _0x1e8f6d)
          .closest(".ui-dialog-content")
          .dialog("close")
      : $("#" + _0x1e8f6d)
          .closest(".ui-dialog-content")
          .dialog("close");
}
(function (_0x1a9c5c) {
  var _0x1f9761 = _0x1a9c5c.ui.dialog.prototype._init;
  ((_0x1a9c5c.ui.dialog.prototype._init = function () {
    var _0x392234 = this;
    (_0x1f9761.apply(this, arguments),
      (uiDialogTitlebar = this["uiDialogTitlebar"]),
      uiDialogTitlebar.append(
        '<a href="#" class="dialog-maximize ui-dialog-titlebar-max ui-corner-all" role="button"><span class="ui-icon ui-icon-newwin" title="Maximizar/Minimizar">maximizar</span></a>',
      ),
      uiDialogTitlebar.append(
        '<a href="#" class="dialog-mais ui-dialog-titlebar-plu ui-corner-all" role="button"><i class="fa fa-bars" style="padding: 3px 4px 4px 4px;" title="Mais Opções"></i></a>',
      ),
      uiDialogTitlebar.append(
        '<a href="#" class="dialog-refresh ui-dialog-titlebar-ref ui-corner-all" role="button"><span class="ui-icon ui-icon-refresh" title="Atualizar Janela">atualizar</span></a>',
      ),
      (this["uiDialogTitlebarMin"] = _0x1a9c5c(
        ".dialog-minimize",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.minimize(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-maximize",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.maximize(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-refresh",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.refresh(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-info",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.info(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-auditoria",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.auditoria(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-mais",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.mais(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-trocaremp",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.trocaremp(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-anterior",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.anterior(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-proximo",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.proximo(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-primeiro",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.primeiro(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-ultimo",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.ultimo(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-incluir",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.incluir(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-duplicar",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.duplicar(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-procurar",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.procurar(), false);
        })),
      (this["uiDialogTitlebarRest"] = _0x1a9c5c(
        ".dialog-excluir",
        uiDialogTitlebar,
      )
        .hover(
          function () {
            _0x1a9c5c(this).addClass("ui-state-hover");
          },
          function () {
            _0x1a9c5c(this).removeClass("ui-state-hover");
          },
        )
        .click(function () {
          return (_0x392234.excluir(), false);
        })));
  }),
    _0x1a9c5c.extend(_0x1a9c5c.ui.dialog.prototype, {
      maximize: function () {
        maximizeDialog(_0x1a9c5c(this["uiDialog"]));
      },
      minimize: function () {
        if (this["element"].data("janelaIdRoot")) {
          var _0xd31d81 = this["element"].data("janelaIdRoot");
          _0x1a9c5c("#toolbar-btn-" + _0xd31d81).trigger("click");
        }
      },
      refresh: function () {
        _0x1a9c5c(".list-options-form").remove();
        if (this["element"].data("janelaId")) {
          var _0x2398df = this["element"].data("janelaIdRoot"),
            _0x52e448 = this["element"].data("janelaId"),
            _0x1731b0 = this["element"].data("janelaIdParent"),
            _0x3ba834 = this["element"].data("janelaIdParentChain"),
            _0x5366e8 = this["element"].data("janelaVars"),
            _0x228d26 = this["element"].data("janelaHref"),
            _0xbf71ad = _0x1a9c5c.ajax({
              url: _0x228d26,
              dataType: "html",
              data:
                "ajax=true&divId=" +
                encodeURIComponent(_0x52e448) +
                "&divIdRoot=" +
                encodeURIComponent(_0x2398df) +
                "&divIdParent=" +
                encodeURIComponent(_0x1731b0) +
                "&divIdParentChain=" +
                encodeURIComponent(_0x3ba834) +
                _0x5366e8,
              type: "post",
              success: function (_0x3ce84e) {
                (_0x1a9c5c("#" + _0x52e448).html(_0x3ce84e),
                  reloadUI(_0x52e448),
                  corrigeGrid(_0x52e448));
              },
            });
          (typeof requisicoesAjax[_0x52e448] == "undefined" &&
            (requisicoesAjax[_0x52e448] = []),
            requisicoesAjax[_0x52e448].push(_0xbf71ad));
        }
      },
      info: function () {
        diagramaPrimeraJanela(this["element"]);
      },
      auditoria: function () {
        if (this["element"].data("janelaId")) {
          var _0x22674b = _0x1a9c5c(this["element"])
            .parent()
            .find(".ui-icon-note");
          gridAudit(
            this["element"].parent().find(".content:eq(0)"),
            _0x22674b.data("table"),
            _0x22674b.data("id"),
          );
        }
      },
      mais: function () {
        if (this["element"].data("janelaId")) {
          var _0x1654eb = _0x1a9c5c(this["element"]).parent().find(".fa-bars");
          janelaMaisOpcoes(_0x1654eb);
        }
      },
      trocaremp: function () {
        if (this["element"].data("janelaId")) {
          var _0x2ed4c9 = _0x1a9c5c(this["element"])
            .parent()
            .find(".ui-icon-note");
          trocarEmp(
            this["element"].parent().find(".content:eq(0)"),
            _0x2ed4c9.data("table"),
            _0x2ed4c9.data("id"),
          );
        }
      },
      anterior: function () {
        if (this["element"].data("janelaId")) {
          var _0x16d115 = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-triangle-1-w"),
            _0x31fdd6 = _0x16d115.data("table"),
            _0x1150ed = _0x16d115.data("id");
          voltarId(
            this["element"].parent().find(".content:eq(0)"),
            _0x1150ed,
            _0x31fdd6,
            _0x16d115,
          );
        }
      },
      proximo: function () {
        if (this["element"].data("janelaId")) {
          var _0x55165b = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-triangle-1-w"),
            _0x560006 = _0x55165b.data("table"),
            _0x484c87 = _0x55165b.data("id");
          avancarId(
            this["element"].parent().find(".content:eq(0)"),
            _0x484c87,
            _0x560006,
            _0x55165b,
          );
        }
      },
      primeiro: function () {
        if (this["element"].data("janelaId")) {
          var _0x2f2782 = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-triangle-1-w"),
            _0x3abd6d = _0x2f2782.data("table"),
            _0x376644 = _0x2f2782.data("id");
          primeiroId(
            this["element"].parent().find(".content:eq(0)"),
            _0x376644,
            _0x3abd6d,
            _0x2f2782,
          );
        }
      },
      ultimo: function () {
        if (this["element"].data("janelaId")) {
          var _0x157e3f = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-triangle-1-w"),
            _0x1a95e0 = _0x157e3f.data("table"),
            _0x37b19e = _0x157e3f.data("id");
          ultimoId(
            this["element"].parent().find(".content:eq(0)"),
            _0x37b19e,
            _0x1a95e0,
            _0x157e3f,
          );
        }
      },
      incluir: function () {
        if (this["element"].data("janelaId")) {
          var _0x5d5083 = this["element"]
              .find(".content:eq(0)")
              .find(".ui-tabs-panel:visible:first")
              .data("divId"),
            _0x1e4946 = _0x1a9c5c("#" + _0x5d5083 + " form:first")
              .parent()
              .data("href"),
            _0x447a1e = _0x1a9c5c("#" + _0x5d5083 + " form:first")
              .parent()
              .data("divIdRoot");
          typeof _0x1e4946 == "undefined" &&
            ((_0x1e4946 = _0x1a9c5c("#" + _0x5d5083).data("href")),
            (_0x447a1e = _0x1a9c5c("#" + _0x5d5083).data("divIdRoot")));
          if (typeof _0x1e4946 != "undefined") {
            var _0x4663fb = _0x1e4946.split("/");
            _0x4663fb[0x3] == "form" &&
              ((classe = _0x4663fb[0x2].toUpperCase()),
              (method = _0x4663fb[0x4]),
              _0x1a9c5c.getJSON(
                "/sistema/funcoes/controleFormulario/novoRegistro/?ajax=true&classe=" +
                  encodeURIComponent(classe) +
                  "&case=" +
                  encodeURIComponent(method) +
                  "&divIdRoot=" +
                  encodeURIComponent(_0x447a1e) +
                  "&divId=" +
                  encodeURIComponent(_0x5d5083) +
                  "&callback=?",
                function (_0x5bcedb) {
                  eval(_0x5bcedb.data);
                },
              ));
          }
        }
      },
      duplicar: function () {
        janelaElem = this["element"];
        if (this["element"].data("janelaId")) {
          var _0x2048af = this["element"]
              .find(".content:eq(0)")
              .find(".ui-tabs-panel:visible:first")
              .data("divId"),
            _0x23fc06 = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-extlink"),
            _0x2c9ad1 = _0x23fc06.data("table"),
            _0x212437 = _0x23fc06.data("id"),
            _0x19c463 = _0x1a9c5c("#" + _0x2048af + " form:first")
              .parent()
              .data("href"),
            _0x1d204a = _0x1a9c5c("#" + _0x2048af + " form:first")
              .parent()
              .data("divIdRoot");
          typeof _0x19c463 == "undefined" &&
            ((_0x19c463 = _0x1a9c5c("#" + _0x2048af).data("href")),
            (_0x1d204a = _0x1a9c5c("#" + _0x2048af).data("divIdRoot")));
          if (typeof _0x19c463 != "undefined") {
            var _0xd4d6a7 = _0x19c463.split("/");
            if (_0xd4d6a7[0x3] == "form") {
              ((classe = _0xd4d6a7[0x2].toUpperCase()),
                (method = _0xd4d6a7[0x4]));
              var _0x1d1888 = {
                Sim: function () {
                  (_0x1a9c5c.getJSON(
                    "/sistema/funcoes/controleFormulario/duplicarRegistro/?ajax=true&classe=" +
                      encodeURIComponent(classe) +
                      "&case=" +
                      encodeURIComponent(method) +
                      "&divIdRoot=" +
                      encodeURIComponent(_0x1d204a) +
                      "&divId=" +
                      encodeURIComponent(_0x2048af) +
                      "&tabela=" +
                      encodeURIComponent(_0x2c9ad1) +
                      "&id=" +
                      encodeURIComponent(_0x212437) +
                      "&callback=?",
                    function (_0x5d1f42) {
                      eval(_0x5d1f42.data);
                    },
                  ),
                    _0x1a9c5c(this).dialog("destroy").remove());
                },
                Não: function () {
                  _0x1a9c5c(this).dialog("destroy").remove();
                },
              };
              dialogConfirm(
                "Atenção",
                "Você deseja duplicar este registro? ",
                _0x1d1888,
              );
            }
          }
        }
      },
      procurar: function () {
        if (this["element"].data("janelaId")) {
          var _0x57d93d = this["element"].find(".content:eq(0)").data("divId"),
            _0x5d1769 = this["element"]
              .find(".content:eq(0)")
              .find(".ui-tabs-panel:visible:first")
              .data("divId"),
            _0x58bcb4 = this["element"].attr("id");
          _0x58bcb4 = _0x1a9c5c("#" + _0x58bcb4).data("janelaHref");
          var _0x448208 = _0x1a9c5c("#" + _0x5d1769 + " form:first")
              .parent()
              .data("divIdRoot"),
            _0x40add7 = _0x1a9c5c("#" + _0x5d1769 + " form:first")
              .parent()
              .data("divId"),
            _0x545392 = _0x1a9c5c("#" + _0x5d1769 + " form:first")
              .parent()
              .data("href");
          typeof _0x545392 == "undefined" &&
            ((_0x545392 = _0x1a9c5c("#" + _0x5d1769).data("href")),
            (_0x448208 = _0x1a9c5c("#" + _0x5d1769).data("divIdRoot")),
            (_0x40add7 = _0x1a9c5c("#" + _0x5d1769).data("divId")));
          if (typeof _0x545392 != "undefined") {
            var _0x59087e = _0x545392.split("/");
            if (_0x59087e[0x3] == "form") {
              var _0x298b0a = _0x1a9c5c("#" + _0x40add7).data("sqlPesquisa"),
                _0x57e9b4 = _0x1a9c5c("#" + _0x40add7).data("labelPesquisa"),
                _0x28215e = _0x1a9c5c("#" + _0x40add7).data("sqlExtra");
              _0x298b0a &&
                eval(
                  "abrirJanela(![], _0x448208, _0x5d1769, unique(), '', _0x1eb97c(1951), _0x1eb97c(1705), _0x1eb97c(683) + encodeURIComponent(_0x298b0a) + _0x1eb97c(506) + encodeURIComponent(_0x28215e) + '&labels=' + encodeURIComponent(_0x57e9b4) + _0x1eb97c(320) + encodeURIComponent(_0x57d93d), [\n    375,\n    75\n]);",
                );
            }
          }
        }
      },
      excluir: function () {
        if (this["element"].data("janelaId")) {
          var _0x16a8f0 = this["element"]
              .find(".content:eq(0)")
              .find(".ui-tabs-panel:visible:first")
              .data("divId"),
            _0x3a7d5a = _0x1a9c5c(this["element"])
              .parent()
              .find(".ui-icon-trash"),
            _0x37badd = _0x3a7d5a.data("table"),
            _0x35e930 = _0x3a7d5a.data("id"),
            _0x45b298 = _0x1a9c5c("#" + _0x16a8f0 + " form:first")
              .parent()
              .data("href");
          typeof _0x45b298 == "undefined" &&
            (_0x45b298 = _0x1a9c5c("#" + _0x16a8f0).data("href"));
          if (typeof _0x45b298 != "undefined") {
            var _0x15a416 = _0x45b298.split("/");
            if (_0x15a416[0x3] == "form") {
              ((classe = _0x15a416[0x2].toUpperCase()),
                (method = _0x15a416[0x4]));
              var _0x31c2a4 = {
                Sim: function () {
                  (_0x1a9c5c.getJSON(
                    "/sistema/funcoes/controleFormulario/excluirRegistro/?ajax=true&classe=" +
                      encodeURIComponent(classe) +
                      "&case=" +
                      encodeURIComponent(method) +
                      "&divId=" +
                      encodeURIComponent(_0x16a8f0) +
                      "&tabela=" +
                      encodeURIComponent(_0x37badd) +
                      "&id=" +
                      encodeURIComponent(_0x35e930) +
                      "&callback=?",
                    function (_0x2e3be7) {
                      eval(_0x2e3be7.data);
                    },
                  ),
                    _0x1a9c5c(this).dialog("destroy").remove());
                },
                Não: function () {
                  _0x1a9c5c(this).dialog("destroy").remove();
                },
              };
              dialogConfirm(
                "Atenção",
                "Você deseja excluir este registro? ",
                _0x31c2a4,
              );
            }
          }
        }
      },
    }));
})(jQuery);
function showHideTabs(_0x4de728, _0x4dc3c5) {
  var _0x4de728 = typeof _0x4de728 == "undefined" ? false : _0x4de728,
    _0x4dc3c5 = typeof _0x4dc3c5 == "undefined" ? -0x2 : _0x4dc3c5;
  if (!_0x4de728) {
    $(".noBorder").size() > 0x0 && $(".noBorder").showLoading();
    var _0x44ffe4 = "",
      _0x30ebf3 = 0x0;
    $(".tabs").each(function () {
      _0x30ebf3++;
      var _0x367497 = $(this),
        _0x5c87df = _0x367497.closest(".content").data("href");
      ((_0x5c87df = _0x5c87df.split("/")),
        (_0x5c87df = _0x5c87df[_0x5c87df.length - 0x2]));
      var _0x4b7067 = [];
      $.getJSON(
        "/sistema/personalizacaoTab/form_func-ajax/mostrarTabsOcultas/?ajax=true&alterandoPara=" +
          _0x4dc3c5 +
          "&tabId=" +
          encodeURIComponent(_0x5c87df) +
          "&callback=?",
        function (_0x584150) {
          (_0x584150.data.length > 0x0 && (_0x4b7067 = _0x584150.data),
            $.getJSON(
              "/sistema/personalizacaoTab/form_func-ajax/perfisTabs/?ajax=true&alterandoPara=" +
                _0x4dc3c5 +
                "&tabId=" +
                encodeURIComponent(_0x5c87df) +
                "&callback=?",
              function (_0x58becd) {
                ((_0x44ffe4 +=
                  '<span style="font-size: 1.2em;">' +
                  _0x5c87df +
                  "</span><br/>"),
                  (_0x44ffe4 += _0x58becd.data),
                  _0x367497.find("> .ui-tabs-panel").each(function () {
                    var _0x1721c0 = _0x367497
                        .find(
                          "> .ui-tabs-nav > li:nth-child(" +
                            $(this).index() +
                            ")",
                        )
                        .text(),
                      _0x15b426 = "";
                    ($.each(_0x4b7067, function (_0x2aa649, _0x2eafac) {
                      _0x2eafac == _0x1721c0 &&
                        (_0x15b426 = 'checked="checked"');
                    }),
                      (_0x44ffe4 +=
                        "&nbsp;&nbsp;&nbsp;&nbsp;" +
                        '<input type="checkbox" tabId="' +
                        _0x5c87df +
                        '" tabtext="' +
                        _0x1721c0 +
                        '" onclick="showHideTabs(this, ' +
                        _0x4dc3c5 +
                        ');" ' +
                        _0x15b426 +
                        '<span style="font-weight: bold;">' +
                        _0x1721c0 +
                        "</span>&nbsp;(" +
                        $(this).data("href") +
                        ")<br/>"));
                  }),
                  (_0x44ffe4 += "<br />"),
                  _0x30ebf3--);
              },
            ));
        },
      );
    });
    var _0x161878 = setInterval(function () {
      if (_0x30ebf3 > 0x0) returnfalse;
      clearInterval(_0x161878);
      $(".noBorder").size() > 0x0 && $(".noBorder").hideLoading();
      var _0x48d807 = {
        Fechar: function () {
          $(this).dialog("destroy").remove();
        },
      };
      dialogConfirm("Ocultar/Mostrar Abas", _0x44ffe4, _0x48d807);
    }, 0x64);
  } else
    return (
      (_0x4de728 = $(_0x4de728)),
      $.getJSON(
        "/sistema/personalizacaoTab/form_func-ajax/showHideTabs/?ajax=true&checked=" +
          _0x4de728.is(":checked") +
          "&alterandoPara=" +
          _0x4dc3c5 +
          "&tabId=" +
          encodeURIComponent(_0x4de728.attr("tabId")) +
          "&tabtext=" +
          encodeURIComponent(_0x4de728.attr("tabtext")) +
          "&callback=?",
        function (_0x13fa13) {},
      ),
      _0x4de728.prop("checked"),
      false
    );
}
function personalizacaoTabs() {
  var _0x3a1a20 = [];
  ($(".ui-dialog:visible:last .tabs:visible").each(function () {
    var _0x4f62f8 = $(this),
      _0x4f2f59 = _0x4f62f8.closest(".content").data("href");
    _0x3a1a20.push(_0x4f2f59);
  }),
    _0x3a1a20.length == 0x0
      ? alert("Nenhuma tab encontrada.")
      : abrirJanela(
          false,
          divIdRootAberto(),
          "",
          menuId("personalizacaoTabs"),
          "",
          "Personalização das Tabs",
          "/sistema/personalizacaoTab/content/personalizacao/",
          "&tabs=" + encodeURIComponent(JSON.stringify(_0x3a1a20)),
          [0x384, 0x1f4],
        ));
}
function personalizacaoContents() {
  var _0x8cf433 = [];
  ($(
    ".ui-dialog:visible:last .ui-dialog-content, .ui-dialog:visible:last .content",
  ).each(function () {
    var _0x1d8cce = $(this),
      _0x32af83 = _0x1d8cce.data("href")
        ? _0x1d8cce.data("href")
        : _0x1d8cce.data("janelaHref");
    if (_0x32af83) {
      _0x32af83 = $.trim(_0x32af83);
      var _0xc58fb5 = _0x32af83.split("/");
      _0xc58fb5[0x3] == "content" && _0x8cf433.push(_0x32af83);
    }
  }),
    _0x8cf433.length == 0x0
      ? alert("Nenhum content encontrado.")
      : abrirJanela(
          false,
          divIdRootAberto(),
          "",
          menuId("personalizacaoContents"),
          "",
          "Personalização dos Contents",
          "/sistema/personalizacaoContent/content/personalizacao/",
          "&contents=" + encodeURIComponent(JSON.stringify(_0x8cf433)),
          [0x384, 0x1f4],
        ));
}
function personalizacaoBotoes() {
  $(".frameworkBotoes:visible").each(function () {
    var _0x5b1767 = $(this),
      _0x254bcb = gridPegarDivId(_0x5b1767);
    !_0x5b1767.hasClass("botaoEditar")
      ? (_0x5b1767.addClass("botaoEditar"),
        _0x5b1767.find(".botaoAdd").css({ display: "block" }),
        _0x5b1767.find("button").each(function () {
          var _0x4f170c = $(this);
          (_0x4f170c.unbind("click"),
            _0x4f170c.attr(
              "onclick",
              "editarBotao(event, '" +
                _0x4f170c.attr("botaoId") +
                "'); return false;",
            ));
          if (
            _0x4f170c.hasClass("botaoOculto") ||
            _0x4f170c.hasClass("botaoDesabilitado")
          ) {
            _0x4f170c.css({ display: "inline", opacity: "0.5" });
            if (_0x4f170c.hasClass("botaoDesabilitado")) {
              _0x4f170c.removeAttr("disabled");
            }
          }
        }))
      : divRefresh(_0x254bcb, true);
  });
}
function personalizacaoForms() {
  var _0x3732cc = [];
  ($(".ui-dialog:visible:last .formFormulario").each(function () {
    var _0x2ad6cd = $(this).closest(".content").data("href"),
      _0x5130b3 = $(this).closest(".content").data("divId"),
      _0x2574b5 = _0x2ad6cd.split("/");
    (_0x2574b5[0x3] != "form" &&
      ((_0x2ad6cd = $("#" + gridPegarDivId(this)).data("href")),
      (_0x5130b3 = $("#" + gridPegarDivId(this)).data("divId")),
      (_0x2574b5 = _0x2ad6cd.split("/")),
      _0x2574b5[0x3] != "form" &&
        ((_0x2ad6cd = $(this)
          .find(".ui-tabs-panel:visible:first")
          .data("href")),
        (_0x5130b3 = $(this)
          .find(".ui-tabs-panel:visible:first")
          .data("divId")))),
      _0x3732cc.push([_0x2ad6cd, _0x5130b3]),
      divSetarAcaoId(_0x5130b3, "true", "pegarForm"),
      divRefresh(
        _0x5130b3,
        true,
        function () {
          divSetarAcaoId(_0x5130b3, "", "pegarForm");
        },
        false,
      ));
  }),
    _0x3732cc.length == 0x0
      ? alert("Nenhuma tab encontrada.")
      : abrirJanela(
          false,
          divIdRootAberto(),
          "",
          menuId("personalizacaoForms"),
          "",
          "Personalização dos Forms",
          "/sistema/personalizacaoForm/content/personalizacao/",
          "&forms=" + encodeURIComponent(JSON.stringify(_0x3732cc)),
          [0x384, 0x1f4],
        ));
}
function showHideFormEditor(_0xae7da4) {
  var _0xae7da4 = typeof _0xae7da4 == "undefined" ? false : true;
  if (_0xae7da4)
    return (
      $(".ui-dialog.ui-multidraggable").removeClass("ui-multidraggable"),
      $(".ui-multidraggable:visible").size() > 0x0
        ? $(".ui-draggable:visible").removeClass("ui-multidraggable")
        : $(".ui-draggable:visible").addClass("ui-multidraggable"),
      false
    );
  if ($("form .formEmEdicao").size() > 0x0) {
    var _0x367b2a = $("form .formEmEdicao").parent();
    _0x367b2a.is("form") && (_0x367b2a = _0x367b2a.parent());
    (_0x367b2a.css({ background: "", position: "", "z-index": "" }),
      $(".formEditorMaisOpcoes").remove());
    _0x367b2a.is(":visible") &&
      divSetarAcaoId(
        _0x367b2a.attr("id"),
        gerarCodeFormEditor(),
        "formEditorData",
      );
    var _0x5361cb = {};
    ($("form .formEmEdicao")
      .removeClass("formEmEdicao")
      .find(".formEditor")
      .each(function () {
        ($(this)
          .css({ opacity: "1", cursor: "auto" })
          .find(".moveHandle")
          .remove(),
          $(this).draggable("destroy"),
          $(this).find(".inputEditorWidth").remove(),
          $(this).find(".inputEditorHeigth").remove());
      }),
      divRefresh(_0x367b2a.attr("id"), true));
  } else {
    if (
      $(".formFormulario:visible:last").find(".formFormatoEditor").size() == 0x0
    ) {
      var _0x5361cb = {};
      ($(".formFormulario:last table:visible .formInput").each(function () {
        if ($(this).attr("type") == "hidden") returntrue;
        var _0x12b7f8 = $(this).closest(".idForm").attr("idform"),
          _0x45fd40 = $(this).position(),
          _0x3065cb = $(
            "form:last table:visible .idLabel-" + _0x12b7f8,
          ).position();
        ((_0x45fd40.top = Math.floor(_0x45fd40.top)),
          (_0x45fd40.left = Math.floor(_0x45fd40.left)),
          (_0x3065cb.top = Math.floor(_0x3065cb.top)),
          (_0x3065cb.left = Math.floor(_0x3065cb.left)),
          (_0x5361cb[$(this).attr("id")] = {
            top: _0x3065cb.top,
            left: _0x3065cb.left,
          }),
          (_0x5361cb[$(this).attr("id")].width = $(this).width()));
      }),
        $(".formFormulario .formFormatoEditor:visible:last .formEditor").css(
          "color",
          "inherit",
        ));
      var _0xffdac7 = JSON.stringify(_0x5361cb);
      return (alert(_0xffdac7), console.log(_0xffdac7), false);
    }
    if (
      $(
        ".formFormulario .formFormatoEditor:visible:last .itemForm-Tab_Icms_Produto",
      ).size() != 0x0
    )
      return (alert("Editor de formulário desabilitado."), false);
    var _0x2d76d1 = $(".formFormulario .formFormatoEditor:visible:last"),
      _0x232aa0 = _0x2d76d1.addClass("formEmEdicao").parent(),
      _0x766457 = gridPegarDivId(_0x232aa0);
    (divSetarAcaoId(_0x766457, "true", "pegarForm"),
      divRefresh(
        _0x766457,
        true,
        function () {
          divSetarAcaoId(_0x766457, "", "pegarForm");
        },
        false,
      ));
    _0x232aa0.is("form") && (_0x232aa0 = _0x232aa0.parent());
    if (_0x232aa0.data("divIdRoot") == "B.I.") {
      _0x232aa0.css({
        background:
          'url("/hardness3/static/img/editorFormGridBackground2.gif") repeat scroll 0 1px transparent',
      });
      var _0x50a0d9 = 0xd,
        _0x2f0838 = 0xd;
    } else {
      (_0x232aa0.height(_0x232aa0.height()),
        _0x232aa0.css({
          position: "absolute",
          width: "calc(100% - 5px)",
          background:
            'url("/hardness3/static/img/editorFormGridBackground.gif") repeat scroll 0 7px #EDE7DE',
          "z-index": "9999999",
          overflow: "visible",
        }));
      var _0x50a0d9 = 0x10,
        _0x2f0838 = 0x17;
    }
    var _0xc2c3bc = $("<div></div>")
      .addClass("formEditorMaisOpcoes")
      .css({ position: "absolute", top: "7px", right: "7px" })
      .appendTo(_0x232aa0);
    $('<button><i class="fas fa-bars" title="Mais opções"></i></button>')
      .css({
        color: "rgb(0 0 0 / 70%)",
        cursor: "pointer",
        width: "30px",
        height: "20px",
      })
      .bind("click", function (_0x2bdda8) {
        (_0x2bdda8.preventDefault(), _0x2bdda8.stopPropagation());
        var _0x525dce = $(this).closest(".formEditorMaisOpcoes"),
          _0x5b33b8 = $("#list-options-form");
        ($("#formEmEdicaoCamposOcultosCombo").hide(),
          $("#formEmEdicaoCamposHiddenCombo").hide());
        if (_0x5b33b8.length > 0x0) _0x5b33b8.remove();
        else {
          var _0x5b33b8 = $("<ul></ul>")
            .addClass("dropdown-menu list-options-form")
            .attr("id", "list-options-form")
            .attr("role", "menu")
            .css({ display: "block", position: "absolute" });
          ($(
            '<li role="presentation" style="text-align: left;"><a class="acaoCamposOcultos" role="menuitem" tabindex="-1" href="#">Campos Ocultos</a></li>',
          ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;"><a class="acaoCamposHidden" role="menuitem" tabindex="-1" href="#">Campos Hidden</a></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;"><a class="acaoSelecionarTodos" role="menuitem" tabindex="-1" href="#">Selecionar Todos</a></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;"><a class="acaoReposicionarSelecionados" role="menuitem" tabindex="-1" href="#">Reposicionar Selecionados</a></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;" class="divider"></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;"><a class="acaoCopiarFormEditor" role="menuitem" tabindex="-1" href="#">Copiar form_editor</a></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;" class="divider"></li>',
            ).appendTo(_0x5b33b8),
            $(
              '<li role="presentation" style="text-align: left;"><a class="acaoSalvarFecharEditor" role="menuitem" tabindex="-1" href="#">Salvar e Fechar Editor</a></li>',
            ).appendTo(_0x5b33b8),
            $("body").append(_0x5b33b8));
          var _0x56d994 = _0x525dce.offset().top + _0x525dce.height(),
            _0x5dfbdc =
              _0x525dce.offset().left + _0x525dce.width() - _0x5b33b8.width();
          (_0x5b33b8.css({ top: _0x56d994, left: _0x5dfbdc }),
            _0x5b33b8.find(".acaoCamposOcultos").bind("click", function () {
              return (
                $("#formEmEdicaoCamposOcultosCombo").toggle(),
                $(".list-options-form").remove(),
                false
              );
            }),
            _0x5b33b8.find(".acaoCamposHidden").bind("click", function () {
              return (
                $("#formEmEdicaoCamposHiddenCombo").toggle(),
                $(".list-options-form").remove(),
                false
              );
            }),
            _0x5b33b8.find(".acaoSelecionarTodos").bind("click", function () {
              return (
                showHideFormEditor(true),
                $(".list-options-form").remove(),
                false
              );
            }),
            _0x5b33b8
              .find(".acaoReposicionarSelecionados")
              .bind("click", function () {
                return (
                  $(
                    ".formFormulario .formEditor.ui-draggable.ui-multidraggable",
                  ).each(function () {
                    $(this).css(
                      "left",
                      0x60 - $(this).find(".itemForm").position().left,
                    );
                  }),
                  $(".list-options-form").remove(),
                  false
                );
              }),
            _0x5b33b8
              .find(".acaoCopiarFormEditor")
              .bind("click", async function () {
                const _0x52e53d = await copyToClipboard(gerarCodeFormEditor());
                return (
                  _0x52e53d
                    ? dialogConfirm(
                        "Sucesso",
                        "Código do form_editor copiado com sucesso!",
                      )
                    : dialogConfirm(
                        "Erro",
                        "Não foi possivel copiar o código do form_editor. Por favor, contate o suporte técnico.",
                      ),
                  $(".list-options-form").remove(),
                  false
                );
              }),
            _0x5b33b8
              .find(".acaoSalvarFecharEditor")
              .bind("click", function () {
                return (
                  showHideFormEditor(),
                  $("#formEmEdicaoCamposOcultosCombo").remove(),
                  $("#formEmEdicaoCamposHiddenCombo").remove(),
                  $(".formEditorMaisOpcoes").remove(),
                  false
                );
              }));
        }
      })
      .appendTo(_0xc2c3bc);
    const _0x47363c = 0x1b,
      _0x306d9c = 0x1b;
    var _0x1fad4a = $("<ul>")
        .attr("id", "formEmEdicaoCamposOcultosCombo")
        .addClass("dropdown-menu")
        .css({
          display: "none",
          position: "absolute",
          top: _0x47363c + "px",
          left: "unset",
          right: "6px",
          "min-width": "150px",
          "overflow-y": "auto",
        }),
      _0x438b36 = 0x12c,
      _0x4011bc = _0x232aa0.height() - 0x14 - _0x47363c;
    if (_0x4011bc > _0x438b36)
      _0x1fad4a.css({
        "min-height": _0x438b36 + "px",
        "max-height": _0x4011bc + "px",
      });
    else
      _0x4011bc <= _0x438b36 &&
        _0x1fad4a.css({
          "min-height": _0x4011bc + "px",
          "max-height": _0x4011bc + "px",
        });
    _0xc2c3bc.after(_0x1fad4a);
    var _0x2a6b02 = $("<ul>")
        .attr("id", "formEmEdicaoCamposHiddenCombo")
        .addClass("dropdown-menu")
        .css({
          display: "none",
          position: "absolute",
          top: _0x306d9c + "px",
          left: "unset",
          right: "6px",
          "min-width": "150px",
          "overflow-y": "auto",
        }),
      _0x55da27 = 0x12c,
      _0x4011bc = _0x232aa0.height() - 0x14 - _0x306d9c;
    if (_0x4011bc > _0x55da27)
      _0x2a6b02.css({
        "min-height": _0x55da27 + "px",
        "max-height": _0x4011bc + "px",
      });
    else
      _0x4011bc <= _0x55da27 &&
        _0x2a6b02.css({
          "min-height": _0x4011bc + "px",
          "max-height": _0x4011bc + "px",
        });
    (_0xc2c3bc.after(_0x2a6b02),
      $(".formFormulario .formFormatoEditor:visible:last .formEditor").each(
        function () {
          if ($(this).is(":hidden")) {
            var _0x146b1b = $(this).attr("formeditorid"),
              _0x3d7c27 =
                $(this).find(".itemForm:first").attr("display-type") ??
                "normal";
            if (_0x3d7c27 == "normal") {
              var _0x53f950 = $("<li>")
                  .attr("class", "liHiddenFormItem")
                  .css({ cursor: "pointer" })
                  .data("originalFormField", $(this)),
                _0x13d850 = $("<a>").html(_0x146b1b);
              (_0x53f950.append(_0x13d850),
                _0x53f950.appendTo("#formEmEdicaoCamposOcultosCombo"));
            } else {
              if (_0x3d7c27 == "hidden") {
                var _0x53f950 = $("<li>")
                    .attr("class", "liHiddenFormItem")
                    .css({ cursor: "pointer" })
                    .data("originalFormField", $(this)),
                  _0x13d850 = $("<a>").html(_0x146b1b);
                (_0x53f950.append(_0x13d850),
                  _0x53f950.bind("click", function (_0x3af6c9) {
                    var _0x3c0fa1 = $(this).data("originalFormField"),
                      _0x1e12c2 = _0x3c0fa1.find(".itemForm"),
                      _0x4c3f84 = _0x1e12c2.hasClass(
                        "campo_input_pesquisa_grid",
                      )
                        ? "pesquisaGrid"
                        : "normal",
                      _0x898808 = _0x1e12c2.attr("id"),
                      _0x11c6ae = _0x1e12c2.prop("tagName");
                    ((_0x898808 = _0x898808.replace(/\_Pesquisa$/, "")),
                      (_0x898808 = _0x898808.replace(/\_disabled$/, "")),
                      _0x11c6ae == "INPUT" &&
                        _0x1e12c2.prop("type") == "button" &&
                        (_0x11c6ae = "button"),
                      abrirJanela(
                        _0x3af6c9,
                        _0x232aa0.data("divIdRoot"),
                        _0x232aa0.data("divId"),
                        unique(),
                        "",
                        "Personalização do Form: " + _0x898808,
                        "/sistema/personalizacaoForm/content/sistemaPFormPrincipal/",
                        "&campo=" +
                          encodeURIComponent(_0x898808) +
                          "&href=" +
                          encodeURIComponent(_0x232aa0.data("href")) +
                          "&tagName=" +
                          encodeURIComponent(_0x11c6ae) +
                          "&type=" +
                          encodeURIComponent(_0x4c3f84),
                        [0x2bc, 0x1e0],
                      ));
                  }),
                  _0x53f950.appendTo("#formEmEdicaoCamposHiddenCombo"));
              }
            }
          }
          ($("<div>")
            .addClass("moveHandle")
            .css({
              width: "100%",
              height: "100%",
              position: "absolute",
              top: "0",
            })
            .bind("mouseup", function (_0x2e268c) {
              var _0x4c91cd = $(this).closest(".formEditor");
              if (!_0x4c91cd.hasClass("movingItem") && !_0x2e268c.ctrlKey) {
                var _0x26fa8c = _0x4c91cd.find(".itemForm"),
                  _0x12ec61 = _0x26fa8c.hasClass("campo_input_pesquisa_grid")
                    ? "pesquisaGrid"
                    : "normal",
                  _0x596b53 = _0x26fa8c.attr("id"),
                  _0x3d5557 = _0x26fa8c.prop("tagName");
                ((_0x596b53 = _0x596b53.replace(/\_Pesquisa$/, "")),
                  (_0x596b53 = _0x596b53.replace(/\_disabled$/, "")),
                  _0x3d5557 == "INPUT" &&
                    _0x26fa8c.prop("type") == "button" &&
                    (_0x3d5557 = "button"),
                  abrirJanela(
                    _0x2e268c,
                    _0x232aa0.data("divIdRoot"),
                    _0x232aa0.data("divId"),
                    unique(),
                    "",
                    "Personalização do Form: " + _0x596b53,
                    "/sistema/personalizacaoForm/content/sistemaPFormPrincipal/",
                    "&campo=" +
                      encodeURIComponent(_0x596b53) +
                      "&href=" +
                      encodeURIComponent(_0x232aa0.data("href")) +
                      "&tagName=" +
                      encodeURIComponent(_0x3d5557) +
                      "&type=" +
                      encodeURIComponent(_0x12ec61),
                    [0x2bc, 0x1e0],
                  ));
              }
              $(".movingItem").removeClass("movingItem");
            })
            .appendTo($(this)),
            $(this).css({ cursor: "pointer" }));
          $(this).find(".itemForm").attr("alterado") == "true" &&
            $(this).css("color", "blue");
          $(this).multidraggable({
            handle: ".moveHandle",
            cursor: "move",
            start: function (_0x161f7a, _0x43f232) {
              ($(this).addClass("movingItem"),
                $(this).find(".moveHandle").css({ cursor: "move" }));
            },
            stop: function (_0x33d72c, _0x41e5d1) {
              ($(this).removeClass("movingItem"),
                $(this).find(".moveHandle").css({ cursor: "pointer" }));
              var _0x2399c7 = $(".formEditorMaisOpcoes"),
                _0x278adb = _0x2399c7.offset(),
                _0x422135 = _0x2399c7.width(),
                _0x352ed8 = _0x2399c7.height(),
                _0x28c1d2 = _0x33d72c.clientX,
                _0x10ef20 = _0x33d72c.clientY;
              if (
                _0x28c1d2 >= _0x278adb.left &&
                _0x28c1d2 <= _0x278adb.left + _0x422135 &&
                _0x10ef20 >= _0x278adb.top &&
                _0x10ef20 <= _0x278adb.top + _0x352ed8
              ) {
                var _0x6c1292 = $(this).attr("formeditorid"),
                  _0x5cfbfa = $("<li>")
                    .attr("class", "liHiddenFormItem")
                    .css({ cursor: "pointer" })
                    .data("originalFormField", $(this)),
                  _0x4c2d98 = $("<a>").html(_0x6c1292);
                (_0x5cfbfa.append(_0x4c2d98),
                  _0x5cfbfa.appendTo("#formEmEdicaoCamposOcultosCombo"),
                  _0x2446bf(_0x5cfbfa),
                  $(this).attr("formeditorhidden", 0x1).hide());
              } else {
                var _0x32b128 = $(this).find("input:not(:hidden)");
                _0x32b128.size() == 0x0 &&
                  (_0x32b128 = $(this).find("textarea"));
                _0x32b128.size() == 0x0 && (_0x32b128 = $(this).find("select"));
                _0x32b128.size() == 0x0 && (_0x32b128 = $(this));
                var _0x238521 = $(this).position(),
                  _0x35eb0b = _0x32b128.position();
                if (
                  _0x32b128.attr("id") &&
                  _0x32b128.attr("id").match("etc011FormDash")
                ) {
                  var _0x3dc264 = _0x238521.top - (_0x238521.top % _0x2f0838),
                    _0x561f7c = _0x238521.left - (_0x238521.left % _0x2f0838);
                  $(this).css({
                    top: _0x3dc264 + "px",
                    left: _0x561f7c + "px",
                  });
                } else {
                  var _0x305fe1 = _0x238521.top % _0x2f0838;
                  _0x305fe1 <= 0xb
                    ? (_0x305fe1 = _0x238521.top - _0x305fe1)
                    : (_0x305fe1 = _0x238521.top - _0x305fe1 + _0x2f0838);
                  _0x35eb0b.left = _0x238521.left + _0x35eb0b.left;
                  var _0x342821 = _0x35eb0b.left % _0x50a0d9;
                  (_0x342821 <= 0x8
                    ? (_0x342821 = _0x238521.left - _0x342821)
                    : (_0x342821 = _0x238521.left - _0x342821 + _0x50a0d9),
                    $(this).find(".campo_input_pesquisa").size() > 0x0 &&
                      (_0x342821 += 0x1),
                    ($(this).find("input[type=submit]").size() > 0x0 ||
                      $(this).find("input[type=button]").size() > 0x0) &&
                      (_0x305fe1 -= 0x2),
                    $(this).find("table").size() > 0x0 && (_0x305fe1 -= 0x3),
                    $(this).css({
                      top: _0x305fe1 + 0x7 + "px",
                      left: _0x342821 + "px",
                    }));
                }
              }
            },
          });
          if ($(this).attr("formEditorDIV") == "1")
            var _0x8491cb = $("<div>")
                .addClass("inputEditorWidth ui-icon ui-icon-close")
                .css({
                  width: "16px",
                  height: "16px",
                  position: "absolute",
                  top: "0",
                  right: "38px",
                  cursor: "pointer",
                  overflow: "visible",
                })
                .data("originalWidth", $(this).width())
                .appendTo($(this))
                .click(function () {
                  $(this).parent().remove();
                }),
              _0x8491cb = $("<div>")
                .addClass("inputEditorWidth ui-icon ui-icon-arrow-2-e-w")
                .css({
                  width: "16px",
                  height: "16px",
                  position: "absolute",
                  top: "0",
                  right: "54px",
                  cursor: "e-resize",
                  overflow: "visible",
                })
                .data("originalWidth", $(this).width())
                .appendTo($(this))
                .draggable({
                  axis: "x",
                  drag: function (_0x1fb4a4, _0x55c072) {
                    var _0x7c43a8 =
                      _0x55c072.position.left - _0x55c072.originalPosition.left;
                    $(this)
                      .parent()
                      .width($(this).data("originalWidth") + _0x7c43a8);
                  },
                  stop: function (_0x433da2, _0x2214fa) {
                    var _0x39aac1 = $(this).parent().width(),
                      _0x27764e = $(this).closest(".formEditor").width(),
                      _0x271b41 = $(this).closest(".formEditor").position(),
                      _0x1789ad = _0x27764e - _0x39aac1 + _0x271b41.left,
                      _0x4205fe = _0x1789ad + _0x39aac1,
                      _0x4205fe = _0x4205fe % _0x50a0d9;
                    (_0x4205fe > 0x0 && (_0x39aac1 -= _0x4205fe),
                      $(this).parent().width(_0x39aac1),
                      $(this).data("originalWidth", _0x39aac1));
                  },
                }),
              _0x8491cb = $("<div>")
                .addClass("inputEditorheight ui-icon ui-icon-arrow-2-n-s")
                .css({
                  height: "16px",
                  height: "16px",
                  position: "absolute",
                  top: "0",
                  right: "70px",
                  cursor: "n-resize",
                  overflow: "visible",
                })
                .data("originalHeight", $(this).height())
                .appendTo($(this))
                .draggable({
                  axis: "y",
                  drag: function (_0x10d6fe, _0x3c4b98) {
                    var _0x452723 =
                      _0x3c4b98.position.top - _0x3c4b98.originalPosition.top;
                    $(this)
                      .parent()
                      .height($(this).data("originalHeight") + _0x452723);
                  },
                  stop: function (_0x35a40d, _0x2e7dd1) {
                    var _0x1f7353 = $(this).parent().height(),
                      _0x340677 = $(this).closest(".formEditor").height(),
                      _0x4d2898 = $(this).closest(".formEditor").position(),
                      _0x4b146a = _0x340677 - _0x1f7353 + _0x4d2898.top,
                      _0x5b7bee = _0x4b146a + _0x1f7353,
                      _0x5b7bee = _0x5b7bee % _0x2f0838;
                    (_0x5b7bee > 0x0 && (_0x1f7353 -= _0x5b7bee),
                      $(this).parent().height(_0x1f7353),
                      $(this).data("originalHeight", _0x1f7353));
                  },
                });
          else {
            var _0x58c3f9 = "0px";
            $(this).find(".itemForm").is("select") && (_0x58c3f9 = "18px");
            var _0x8491cb = $("<div>")
              .addClass("editorFormHelper inputEditorWidth")
              .css({
                width: "5px",
                height: $(this).height() + "px",
                position: "absolute",
                top: "0",
                right: _0x58c3f9,
                cursor: "e-resize",
                "z-index": "3",
                display: "block",
              })
              .data("originalWidth", $(this).find(".formInput").width())
              .appendTo($(this).find(".formInput:first").parent())
              .draggable({
                axis: "x",
                drag: function (_0x13f5de, _0x392b89) {
                  var _0x4a2d37 =
                    _0x392b89.position.left - _0x392b89.originalPosition.left;
                  $(this)
                    .parent()
                    .find(".formInput")
                    .width($(this).data("originalWidth") + _0x4a2d37);
                },
                stop: function (_0x2013f0, _0x757e2c) {
                  var _0x665b95 = $(this).parent().find(".formInput").width();
                  if ($(this).parent().find(".formInput").is("select"))
                    var _0x665b95 = $(this)
                      .parent()
                      .find(".formInput")
                      .outerWidth();
                  var _0x4c0637 = $(this).closest(".formEditor").width(),
                    _0x47e77c = $(this).closest(".formEditor").position(),
                    _0x12b9d8 = _0x4c0637 - _0x665b95 + _0x47e77c.left,
                    _0x56976c = _0x12b9d8 + _0x665b95,
                    _0x56976c = _0x56976c % _0x50a0d9;
                  (_0x56976c > 0x0 && (_0x665b95 -= _0x56976c),
                    $(this).parent().find(".formInput").width(_0x665b95),
                    $(this).data("originalWidth", _0x665b95),
                    $(this).css("left", ""),
                    $(this)
                      .next(".inputEditorHeight")
                      .css("width", _0x665b95 + 0x3));
                },
              });
            if ($(this).find("textarea.formInput").size() != 0x0) {
              var _0x4aecb9 = $(this).find("textarea.formInput:first");
              _0x4aecb9.size() > 0x0 &&
                (_0x4aecb9.css("display", "block"),
                $("#" + _0x4aecb9.attr("id") + "Code").remove());
              var _0x8491cb = $("<div>")
                .addClass("editorFormHelper inputEditorHeight")
                .css({
                  width: $(this).find("textarea.formInput").outerWidth() + "px",
                  height: "5px",
                  position: "absolute",
                  bottom: "0",
                  rigth: "0",
                  cursor: "n-resize",
                  "z-index": "3",
                  display: "block",
                })
                .data("originalHeight", $(this).find(".formInput").height())
                .appendTo($(this).find(".formInput:first").parent())
                .draggable({
                  axis: "y",
                  drag: function (_0x1ebfa3, _0x2b21f0) {
                    var _0x5e1208 =
                      _0x2b21f0.position.top - _0x2b21f0.originalPosition.top;
                    $(this)
                      .parent()
                      .find(".formInput")
                      .height($(this).data("originalHeight") + _0x5e1208);
                  },
                  stop: function (_0x595ffe, _0x81dccd) {
                    var _0x111f98 = $(this)
                        .parent()
                        .find(".formInput")
                        .height(),
                      _0x31572f = $(this).closest(".formEditor").height(),
                      _0x35b8f6 = $(this).closest(".formEditor").position(),
                      _0x3827b3 = _0x31572f - _0x111f98 + _0x35b8f6.top,
                      _0x16ff14 = _0x3827b3 + _0x111f98,
                      _0x16ff14 = _0x16ff14 % _0x2f0838;
                    (_0x16ff14 > 0x0 && (_0x111f98 -= _0x16ff14 - 0x4),
                      $(this).parent().find(".formInput").height(_0x111f98),
                      $(this).data("originalHeight", _0x111f98),
                      $(this).css("top", ""),
                      $(this)
                        .prev(".inputEditorWidth")
                        .css("height", _0x111f98 + 0x3));
                  },
                });
            }
          }
        },
      ),
      $("#formEmEdicaoCamposOcultosCombo li").each(function () {
        _0x2446bf($(this));
      }));
    function _0x2446bf(_0x23052e) {
      _0x23052e.multidraggable({
        handle: ".moveHandle",
        helper: "clone",
        appendTo: _0x2d76d1,
        zIndex: 0x98967f,
        start: function (_0x4c38ad, _0xfa3ef3) {
          (_0xfa3ef3.helper.css({
            "min-width": $(this).width(),
            border: "1px solid #cacaca",
            padding: "6px 3px",
            "border-radius": "4px",
            "background-color": "#ffffffff",
            color: "#413a3a",
            "box-shadow": "0 4px 12px rgba(0, 0, 0, 0.1)",
          }),
            $(this).hide());
        },
        stop: function (_0x389a9e, _0x2e2c40) {
          var _0x3fe443 = _0x2e2c40.position,
            _0x519c40 = $(this),
            _0x3511e2 = _0x519c40.data("originalFormField");
          (_0x3511e2
            .css({ top: _0x3fe443.top + "px", left: _0x3fe443.left + "px" })
            .attr("formeditorhidden", 0x0)
            .show(),
            _0x519c40.remove());
        },
      });
    }
  }
}
function novoCampoFormEditor() {
  var _0x5979a1 = $(".formFormulario .formFormatoEditor:visible:last");
  _0x5979a1.parent().is("form") && (_0x5979a1 = _0x5979a1.parent());
  var _0x5c748e = _0x5979a1.parent();
  abrirJanela(
    false,
    _0x5c748e.data("divIdRoot"),
    _0x5c748e.data("divId"),
    unique(),
    "",
    "Adicionando novos campos: " + _0x5c748e.data("href"),
    "/sistema/personalizacaoForm/content/sistemaPFormNovo/",
    "&campo=" + "&href=" + encodeURIComponent(_0x5c748e.data("href")),
    [0x352, 0x244],
  );
}
function gerarCodeFormEditor() {
  if ($("form .formEmEdicao").size() > 0x0) {
    var _0xfa130a = {};
    return (
      $("form .formEmEdicao")
        .find(".formEditor")
        .each(function () {
          if ($(this).find('.itemForm[display-type="hidden"]').length > 0x0)
            returntrue;
          _0xfa130a[$(this).attr("formEditorID")] = $(this).position();
          if ($(this).attr("formEditorDIV") == "1")
            ((_0xfa130a[$(this).attr("formEditorID")].width = $(this).width()),
              (_0xfa130a[$(this).attr("formEditorID")].height =
                $(this).height()),
              (_0xfa130a[$(this).attr("formEditorID")].tipo = "div"));
          else {
            if ($(this).find(".formInput").is("select"))
              var _0x2cb13b = $(this).find(".formInput").outerWidth();
            else var _0x2cb13b = $(this).find(".formInput").width();
            ($(this).find("textarea.formInput").size() != 0x0 &&
              (_0xfa130a[$(this).attr("formEditorID")].height = $(this)
                .find(".formInput")
                .height()),
              (_0xfa130a[$(this).attr("formEditorID")].width = _0x2cb13b),
              (_0xfa130a[$(this).attr("formEditorID")].hidden =
                $(this).attr("formEditorHidden") == "1" ? 0x1 : 0x0));
          }
        }),
      JSON.stringify(_0xfa130a)
    );
  }
}
function gridPersonalizacao(_0x30b352, _0x2fc2bd) {
  ((_0x30b352[0x3] =
    typeof _0x30b352[0x3] == "undefined" ? "" : _0x30b352[0x3]),
    (_0x2fc2bd = typeof _0x2fc2bd == "undefined" ? "" : 0x1),
    abrirJanela(
      false,
      _0x30b352[0x1],
      _0x30b352[0x2],
      unique(),
      "",
      "Personalização do Grid",
      "/sistema/personalizacaoGrid/content/sistemaPGridPrincipal/",
      "&recover=" +
        _0x2fc2bd +
        "&pesquisaGridId=" +
        encodeURIComponent(_0x30b352[0x3]) +
        "&grid=" +
        encodeURIComponent(_0x30b352[0x0]) +
        _0x30b352[0x4],
      [0x3e8, 0x226],
    ));
}
function dialogConfirm(
  _0x543adc,
  _0x9bda63,
  _0x47c0df,
  _0x281017,
  _0x1e309b,
  _0x213644,
  _0x33bbf3,
) {
  var _0x281017 =
      typeof _0x281017 == "undefined" || !_0x281017 ? false : _0x281017,
    _0x1e309b =
      typeof _0x1e309b == "undefined" || !_0x1e309b ? false : _0x1e309b,
    _0x33bbf3 =
      typeof _0x33bbf3 == "undefined" || !_0x33bbf3 ? false : _0x33bbf3,
    _0x213644 =
      typeof _0x213644 == "undefined" || !_0x213644 ? false : _0x213644;
  if (typeof _0x47c0df == "undefined" || !_0x47c0df || _0x33bbf3) {
    if (_0x33bbf3)
      var _0x47c0df = {
        Fechar: function () {
          $(this).dialog("destroy").remove();
        },
      };
    else
      var _0x47c0df = {
        Fechar: function () {
          $(this).dialog("destroy").remove();
        },
      };
  }
  typeof _0x213644 != "undefined" &&
    _0x213644 &&
    (_0x9bda63 = _0x213644.html());
  var _0x22f7dc = unique(),
    _0xc94a28 = $("<div>")
      .addClass("dialogConfirm")
      .attr({ id: "dc-" + _0x22f7dc })
      .appendTo("body");
  _0x1e309b
    ? $("<p id='" + _0x1e309b + "'>")
        .html(_0x9bda63)
        .appendTo(_0xc94a28)
    : $("<p>").html(_0x9bda63).appendTo(_0xc94a28);
  $("#dc-" + _0x22f7dc).dialog({
    width: "auto",
    height: "auto",
    minWidth: 0xfa,
    resizable: false,
    modal: true,
    title: _0x543adc,
    buttons: _0x47c0df,
    zIndex: 0x270f,
    dialogClass: _0x281017
      ? "ui-dialog-big"
      : _0x33bbf3
        ? "ui-dialog-error"
        : "",
    open: function (_0x4af0c9, _0x30464f) {
      var _0x25ff3c = $(this).parent().find("button:first");
      _0x25ff3c.focus();
    },
  });
  var _0x3dc965 = $("#dc-" + _0x22f7dc).parent();
  return (
    _0x3dc965.find(".ui-dialog-titlebar a").remove(),
    _0x33bbf3 &&
      (_0x3dc965
        .find(".ui-dialog-buttonset")
        .css("width", "100%")
        .find("button:last")
        .css("float", "left"),
      _0x3dc965
        .find(".ui-dialog-buttonset")
        .find("button:first")
        .css("float", "right")),
    _0x22f7dc
  );
}
function dialogAlert(
  _0x3da2a2,
  _0x5e9cf1,
  _0x4f64ae,
  _0x2618fa,
  _0x3494fa,
  _0x282792,
  _0x48f4a2,
) {
  var _0x2618fa =
      typeof _0x2618fa == "undefined" || !_0x2618fa ? false : _0x2618fa,
    _0x3494fa =
      typeof _0x3494fa == "undefined" || !_0x3494fa ? false : _0x3494fa,
    _0x48f4a2 =
      typeof _0x48f4a2 == "undefined" || !_0x48f4a2 ? false : _0x48f4a2,
    _0x282792 =
      typeof _0x282792 == "undefined" || !_0x282792 ? false : _0x282792;
  _0x3da2a2.match(/erro/gi) && (_0x48f4a2 = true);
  typeof _0x282792 != "undefined" &&
    _0x282792 &&
    (_0x5e9cf1 = _0x282792.html());
  var _0x27eb9c = unique(),
    _0x175a6e = $("<div>")
      .addClass("dialogConfirm")
      .attr({ id: "dc-" + _0x27eb9c })
      .appendTo("body");
  _0x3494fa
    ? $("<p id='" + _0x3494fa + "'>")
        .html(_0x5e9cf1)
        .appendTo(_0x175a6e)
    : $("<p>").html(_0x5e9cf1).appendTo(_0x175a6e);
  $("#dc-" + _0x27eb9c).dialog({
    width: "auto",
    height: "auto",
    minWidth: 0xfa,
    resizable: false,
    modal: true,
    title: _0x3da2a2,
    zIndex: 0x270f,
    dialogClass: _0x2618fa
      ? "ui-dialog-big"
      : _0x48f4a2
        ? "ui-dialog-error"
        : "",
  });
  var _0x25926b = $("#dc-" + _0x27eb9c).parent();
  return (
    _0x25926b.find(".ui-dialog-titlebar a").remove(),
    _0x48f4a2 &&
      (_0x25926b
        .find(".ui-dialog-buttonset")
        .css("width", "100%")
        .find("button:last")
        .css("float", "left"),
      _0x25926b
        .find(".ui-dialog-buttonset")
        .find("button:first")
        .css("float", "right")),
    _0x27eb9c
  );
}
function reloadUI(_0x1ccc89, _0x52b008, _0x1fc00b) {
  var _0x52b008 =
      typeof _0x52b008 == "undefined"
        ? $(":focus").hasClass("ui-button") ||
          $(":focus").hasClass("input-editar-campo")
          ? false
          : true
        : _0x52b008,
    _0x1fc00b = typeof _0x1fc00b == "undefined" ? "" : " ." + _0x1fc00b,
    _0x1a7908 = $("#" + _0x1ccc89 + _0x1fc00b);
  (_0x1a7908.find(".tabs").tabs({
    show: function (_0x1f122e, _0x56da80) {
      ($(".colunaAcoes").remove(), $(".list-options-form").remove());
      var _0x557c0e = $(_0x56da80.tab).attr("href").replace(/^#/, ""),
        _0x533635 = $("#" + _0x557c0e);
      _0x533635.find(".content").each(function (_0x3536b7, _0x19605f) {
        (corrigeConteudo($(this).attr("id")), corrigeGrid($(this).attr("id")));
      });
      if (!_0x533635.data("loadOnClick") && _0x533635.data("loaded"))
        moveBtnsToContent(_0x557c0e);
      else {
        if (_0x533635.data("href").indexOf("naocarregar") !== -0x1) return;
        _0x533635.showLoading();
        var _0x1f6f41 = $.ajax({
          url: _0x533635.data("href"),
          dataType: "html",
          type: "POST",
          data:
            "ajax=true&divIdRoot=" +
            _0x533635.data("divIdRoot") +
            "&divIdParent=" +
            _0x533635.data("divIdParent") +
            "&divIdParentChain=" +
            _0x533635.data("divIdParentChain") +
            "&divId=" +
            _0x533635.data("divId") +
            "&divIdChain=" +
            _0x533635.data("divIdChain") +
            _0x533635.data("vars"),
          success: function (_0x286099) {
            return (
              _0x533635.hideLoading(),
              _0x533635.html(_0x286099),
              reloadUI(_0x533635.attr("id")),
              corrigeGrid(_0x533635.attr("id")),
              _0x533635.data({ loaded: true }),
              moveBtnsToContent(_0x533635.attr("id")),
              !isempty(_0x533635.data("callback")) &&
                eval(_0x533635.data("callback")),
              false
            );
          },
        });
        (typeof requisicoesAjax[_0x533635.data("divId")] == "undefined" &&
          (requisicoesAjax[_0x533635.data("divId")] = []),
          requisicoesAjax[_0x533635.data("divId")].push(_0x1f6f41));
      }
      if (
        _0x533635
          .closest(".ui-dialog")
          .find(".ui-dialog-content:first")
          .attr("id") ==
        $(".ui-dialog:visible:last .ui-dialog-content:visible:first").attr("id")
      ) {
        var _0x26e31c = false;
        _0x533635.find("label:visible").each(function () {
          if (_0x26e31c) returnfalse;
          var _0x4e87e1 = $(this).find(
            "input:visible,textarea:visible,select:visible",
          );
          _0x4e87e1.size() != 0x0 &&
            ((_0x26e31c = true),
            _0x4e87e1.closest(".ui-tabs-panel").data("divIdRoot") != "B.I." &&
              !_0x4e87e1.hasClass("campo_input_data") &&
              _0x4e87e1.focus());
        });
      }
      corrigirDirectWrite();
    },
    create: function (_0x304096, _0x27d4f9) {
      _0x1a7908
        .find(".tabs .ui-tabs-nav > li:visible:first > a")
        .trigger("click");
    },
  }),
    _0x1a7908.find("button").button());
  $.timepicker &&
    $.timepicker.regional &&
    $.timepicker.regional["pt-BR"] &&
    $.timepicker.setDefaults($.timepicker.regional["pt-BR"]);
  var _0x41f326 = _0x1a7908.find(".campo_input_data");
  (_0x41f326.datepicker({ dateFormat: "dd/mm/yy" }),
    _0x41f326.datepicker("option", "dayNamesMin", [
      "Dom",
      "Seg",
      "Ter",
      "Qua",
      "Qui",
      "Sex",
      "Sab",
    ]),
    _0x41f326.datepicker("option", "monthNames", [
      "Janeiro",
      "Fevereiro",
      "Março",
      "Abril",
      "Maio",
      "Junho",
      "Julho",
      "Agosto",
      "Setembro",
      "Outubro",
      "Novembro",
      "Dezembro",
    ]));
  var _0x5459d9 = _0x1a7908.find(".campo_input_datahora");
  _0x5459d9.size() > 0x0 &&
    $.fn.datetimepicker &&
    _0x5459d9.datetimepicker({
      dateFormat: "dd/mm/yy",
      timeFormat: "HH:mm:ss",
      controlType: "select",
      closeText: "Enviar",
      dayNamesMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
      monthNames: [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro",
      ],
      timeInput: true,
      onClose: function () {
        var _0x545559 = $(this);
        (_0x545559.hasClass("input-editar-campo") ||
          _0x545559.hasClass("input-editar-campo-rapido")) &&
          (_0x545559.data("datetimepickerSubmit", true),
          _0x545559.removeClass("campo_input_datahora"),
          _0x545559.trigger("blur"));
      },
    });
  (_0x1a7908.find(".grid").each(function () {
    var _0x3a6150 = $(this);
    if (_0x3a6150.hasClass("grid-select"))
      _0x3a6150.find("tbody > tr").css({ cursor: "pointer" });
    else {
    }
  }),
    _0x1a7908.form());
  if (
    _0x1a7908
      .closest(".ui-dialog")
      .find(".ui-dialog-content:first")
      .attr("id") ==
    $(".ui-dialog:visible:last .ui-dialog-content:last:first").attr("id")
  ) {
    if (_0x52b008 && $("#gGeraAlertMsgErroForm").size() == 0x0) {
      var _0x1a7908 = $("#" + _0x1ccc89),
        _0x51cd44 = false;
      _0x1a7908.find("label:visible").each(function () {
        if (_0x51cd44) returnfalse;
        var _0x262be4 = $(this).find(
          "input:visible,textarea:visible,select:visible",
        );
        _0x262be4.size() != 0x0 &&
          ((_0x51cd44 = true),
          _0x262be4.closest(".ui-tabs-panel").data("divIdRoot") != "B.I." &&
            !_0x262be4.hasClass("campo_input_data") &&
            _0x262be4.focus());
      });
    }
  }
  corrigirDirectWrite();
}
function corrigeConteudo(_0x5b1f10) {
  var _0x415e61 = $("#" + _0x5b1f10);
  _0x415e61.closest(".ui-dialog-content").data("callFixEditor") == "true" &&
    fixEditorSize();
  if (_0x415e61.hasClass("content")) {
    if (_0x415e61.hasClass("content-2rows")) _0x415e61.css("height", "49.9%");
    else
      _0x415e61.hasClass("content-3rows")
        ? _0x415e61.css("height", "33.3%")
        : _0x415e61.css("height", "100%");
    var _0x54b67d = _0x415e61.parent(),
      _0x468d72 = _0x54b67d.find("> .buttonsBottom");
    if (_0x468d72.length > 0x0) {
      if (
        !_0x415e61.hasClass("content-2rows") ||
        (_0x415e61.hasClass("content-2rows") &&
          _0x415e61.hasClass("content-2rows-last"))
      ) {
        const _0x3abe40 = getFullHeight(_0x468d72);
        (_0x415e61.height(getFullHeight(_0x415e61) - _0x3abe40 - 0x3),
          _0x54b67d.closest(".content").size() != 0x0 &&
            (_0x468d72.css("bottom", "2px"),
            _0x468d72.find(".buttonsRight").css("margin-right", "0")));
      }
    }
  }
  if (_0x415e61.hasClass("content-2rows")) {
    _0x415e61.css("overflow") == "hidden" &&
      _0x415e61.css({ height: "49.9%", overflow: "auto" });
    if (_0x415e61.hasClass("content-2rows-width")) {
      var _0xbcfb86 = _0x415e61.attr("class"),
        _0x5365c2 = _0xbcfb86.match(/content-2rows-width-([0-9]+)px/);
      _0x415e61.height(_0x5365c2[0x1]);
    } else {
      if (_0x415e61.prev().hasClass("content-2rows-width")) {
        var _0xbcfb86 = _0x415e61.prev().attr("class"),
          _0x5365c2 = _0xbcfb86.match(/content-2rows-width-([0-9]+)px/),
          _0x4e2b91 = _0x5365c2[0x1],
          _0x54b67d = _0x415e61.parent(),
          _0x463532 = 0x0;
        if (!_0x54b67d.hasClass("ui-tabs-panel")) {
          var _0x468d72 = _0x54b67d.find("> .buttonsBottom");
          _0x468d72.length > 0x0 && (_0x463532 = getFullHeight(_0x468d72));
        }
        _0x415e61.height(
          getFullHeight(_0x54b67d) - _0x4e2b91 - _0x463532 - 0x4,
        );
      }
    }
    _0x415e61.find("> .divToScroll > .grid").size() != 0x0 &&
      _0x415e61.css("overflow", "hidden");
  }
  _0x415e61.find(".ui-tabs").each(function (_0x4433e3, _0xada344) {
    var _0x1578c3 = $(this);
    if (_0x1578c3.closest(".content").size() == 0x0) returnfalse;
    var _0x51bcd7 = false;
    _0x1578c3.parent().parent().hasClass("ui-tabs-hide") &&
      ((_0x51bcd7 = true),
      _0x1578c3.parent().parent().removeClass("ui-tabs-hide"));
    var _0x512408 = getFullHeight(_0x415e61.find("> .contentCabecalho"));
    _0x512408 = getFullHeight(_0x415e61) - Number(_0x512408);
    _0x1578c3.closest(".ui-dialog").is($(".ui-dialog:first"))
      ? _0x1578c3.height("100%")
      : _0x1578c3.height(_0x512408);
    var _0x419383 = _0x1578c3.height(),
      _0x8f1c95 = getFullHeight(_0x1578c3.find("> .ui-tabs-nav"));
    (_0x1578c3.find("> .ui-tabs-panel").each(function (_0x12bb1a, _0x381166) {
      $(this).height(_0x419383 - _0x8f1c95 - getExtraVerticalSpace($(this)));
    }),
      _0x51bcd7 && _0x1578c3.parent().parent().addClass("ui-tabs-hide"));
  });
  if (_0x415e61.hasClass("content-3rows")) {
    _0x415e61.css({ height: "33.3%", overflow: "auto" });
    if (_0x415e61.hasClass("content-3rows-width")) {
      var _0xbcfb86 = _0x415e61.attr("class"),
        _0x5365c2 = _0xbcfb86.match(/content-3rows-width-([0-9]+)px/),
        _0x4190a3 = _0x415e61.height() - _0x5365c2[0x1];
      _0x4190a3 > 0x0 &&
        (_0x415e61.height(_0x5365c2[0x1]),
        _0x415e61.data({ "3RowsHeightDiff": _0x4190a3 }),
        _0x415e61
          .find(".ui-tabs")
          .height(_0x415e61.find(".ui-tabs").height() - _0x4190a3),
        _0x415e61
          .find(".ui-tabs-panel")
          .height(_0x415e61.find(".ui-tabs-panel").height() - _0x4190a3));
    } else {
      var _0x1a714f = 0x0,
        _0x5a3084 = _0x415e61.parent().find(".content-3rows-width");
      (_0x5a3084.size() > 0x0 &&
        (_0x1a714f = _0x5a3084.data("3RowsHeightDiff")),
        _0x415e61.hasClass("content-3rows-last") &&
          (_0x415e61.css("margin-top", spaceBtw + "px"),
          (_0x1a714f = _0x1a714f - spaceBtw)),
        _0x415e61.height(_0x415e61.height() + _0x1a714f),
        _0x415e61
          .find(".ui-tabs")
          .height(_0x415e61.find(".ui-tabs").height() + _0x1a714f),
        _0x415e61
          .find(".ui-tabs-panel")
          .height(_0x415e61.find(".ui-tabs-panel").height() + _0x1a714f));
    }
    _0x415e61.find("> .divToScroll > .grid").size() != 0x0 &&
      _0x415e61.css("overflow", "hidden");
  }
  if (_0x415e61.hasClass("content-2columns")) {
    (_0x415e61.css({ width: "10px" }),
      _0x415e61.css({ width: "49.9%", overflow: "auto" }));
    if (_0x415e61.hasClass("content-2columns-width")) {
      var _0xbcfb86 = _0x415e61.attr("class"),
        _0x5365c2 = _0xbcfb86.match(/content-2columns-width-([0-9]+)px/),
        _0x4190a3 = _0x415e61.width() - _0x5365c2[0x1];
      _0x4190a3 > 0x0 &&
        (_0x415e61.width(_0x5365c2[0x1]),
        _0x415e61.data({ "2ColumnsHeightDiff": _0x4190a3 }));
    } else {
      var _0x1a714f = 0x0,
        _0x5a3084 = _0x415e61.parent().find(".content-2columns-width");
      (_0x5a3084.size() > 0x0 &&
        (_0x1a714f = _0x5a3084.data("2ColumnsHeightDiff")),
        _0x415e61.css({ width: "10px" }),
        _0x415e61.css({ width: "49.9%", overflow: "auto" }),
        _0x415e61.width(_0x415e61.width() + _0x1a714f));
    }
  }
}
function corrigeGrid(_0x49fe67, _0x2e735e) {
  _0x2e735e = typeof _0x2e735e == "undefined" ? false : _0x2e735e;
  var _0x12bb10 = $("#" + _0x49fe67);
  setTimeout(function () {
    _0x12bb10.find(".grid").each(function () {
      $this = $(this);
      if ($this.closest(".visuEmail").size() > 0x0) returnfalse;
      $this.parent().prev().find(".dropdown-toggle").dropdown();
      if ($this.closest(".nicEdit-main").size() > 0x0) returntrue;
      $div = $this.parent();
      if (!$div.is(":visible"))
        return (
          $('li [href="#' + _0x49fe67 + '"]').one("click", function () {
            corrigeGrid(_0x49fe67);
          }),
          true
        );
      _0x2e735e &&
        ($div.removeClass("divToScroll"),
        $div.parent().find(".gridTheadMaximizar").remove());
      if ($div.hasClass("divToScroll"))
        return (
          !$this.hasClass("noScrollLoading") &&
            ($div.unbind("scroll"),
            $div.bind("scroll", function () {
              ($(".colunaAcoes").remove(),
                $(".popover")
                  .prev()
                  .popover("destroy")
                  .data("clicked", false)
                  .data("show", false));
              var _0x5da1d7 = $(this).find("table").height() - $(this).height();
              if (
                $(this).scrollTop() > 0x0 &&
                $(this).scrollTop() + 0x64 > _0x5da1d7
              ) {
                var _0x51084f = false;
                $(this).scrollTop() > _0x5da1d7 && (_0x51084f = true);
                var _0x232da2 = gridPegarDivId($(this));
                ($(this).unbind("scroll"),
                  $(
                    '<tr class="trLoading"><td colspan="300" style="text-align: left; padding-left: 128px;">Carregando mais resultados! Por favor, aguarde ...</td></tr>',
                  ).appendTo($(this).find("table.grid > tbody:last")),
                  _0x51084f &&
                    $(this).scrollTop($(this).find("table").height()),
                  divRefresh(
                    _0x232da2,
                    false,
                    function (_0x16a267, _0x2e49e5) {
                      const _0x557378 = _0x2e49e5.find(".divToScroll");
                      _0x557378
                        .find("table.grid > tbody:last tr.trLoading")
                        .remove();
                      var _0x427714 = $(_0x16a267).find(
                        "table.grid > tbody:first > *",
                      );
                      $(_0x16a267).find("table.grid > tbody > *").size() > 0x0
                        ? (_0x427714.appendTo(
                            _0x557378.find("table.grid > tbody"),
                          ),
                          $("<div></div>")
                            .css({
                              display: "none",
                              width: 0x0,
                              height: 0x0,
                              position: "fixed",
                            })
                            .html(_0x16a267)
                            .appendTo("body")
                            .remove(),
                          corrigeGrid(_0x232da2))
                        : _0x557378.bind("scroll", function () {
                            $(".popover")
                              .prev()
                              .popover("destroy")
                              .data("clicked", false)
                              .data("show", false);
                          });
                    },
                    false,
                  ));
              }
            })),
          !$this.hasClass("gridCabecalho") &&
            $this.find("tr").length > 0x0 &&
            !$div.parent().hasClass("removerGridTheadMaximizar") &&
            $div
              .parent()
              .find(".gridTheadMaximizar")
              .css("top", $div.position().top - 0x2),
          updateGridHeadersWidth($this),
          true
        );
      (($parent = $div.parent()),
        $div.addClass("divToScroll"),
        $parent.css({
          overflow: "hidden",
          display: "flex",
          "flex-direction": "column",
        }),
        $div.css({ width: "100%", height: "100%" }));
      if ($this.closest("#oneWindow").size() != 0x0) returnfalse;
      if ($div.find(".naoProcessarThead").size() > 0x0) returnfalse;
      if ($parent.data("divIdRoot") == "importacaoCSV") returnfalse;
      if ($this.hasClass("gridCabecalho")) returnfalse;
      if (
        !$this.hasClass("gridCabecalho") &&
        $this.find("tr").length > 0x0 &&
        !$div.parent().hasClass("removerGridTheadMaximizar")
      ) {
        var _0x1b0982 = unique(),
          _0x31cfd8 = $("<div>")
            .attr({ id: _0x1b0982 })
            .css({ top: $div.position().top - 0x2 })
            .addClass("gridTheadMaximizar");
        (_0x31cfd8.append(
          '<span class="ui-icon ui-icon-triangle-1-n" style="cursor: pointer;"></span>',
        ),
          _0x31cfd8.find("span").bind("click", function () {
            $(this).hasClass("ui-icon-triangle-1-n")
              ? ($(this)
                  .removeClass("ui-icon-triangle-1-n")
                  .addClass("ui-icon-triangle-1-s"),
                maximizarGrid(_0x49fe67))
              : ($(this)
                  .removeClass("ui-icon-triangle-1-s")
                  .addClass("ui-icon-triangle-1-n"),
                minimizarGrid(_0x49fe67));
          }),
          _0x31cfd8.prependTo($div.parent()));
      }
      var _0x33774f = [],
        _0xec4d3b = 0x0,
        _0xd6cfeb = 0x0;
      $this.find("thead th.grid-column-header").each(function () {
        var _0x33bfd1 = $(this),
          _0x50636a = _0x33bfd1.width();
        (_0x33bfd1.css("width", _0x50636a + "px"),
          _0x33bfd1.hasClass("grid-column-freeze") &&
            (_0xec4d3b++,
            _0xec4d3b > 0x1 &&
              ((_0xd6cfeb += _0x33bfd1
                .prevAll(".grid-column-freeze")
                .first()
                .outerWidth()),
              _0x33774f.push(_0xd6cfeb))));
      });
      _0x33774f.length > 0x0 &&
        $this.find("tr").each(function () {
          $(this)
            .find("th.grid-column-freeze, td.grid-column-freeze")
            .each(function (_0x482c66) {
              _0x482c66 > 0x0 &&
                $(this).css("left", 0x1 + _0x33774f[_0x482c66 - 0x1] + "px");
            });
        });
      const _0x49a604 = $this.find("> thead tr");
      _0x49a604.length > 0x1 &&
        _0x49a604.each(function (_0x512d5e) {
          if (_0x512d5e == 0x0) return;
          var _0x3a7931 = 0x0;
          let _0x492fb6 = $(this);
          (_0x492fb6.prevAll("tr").each(function () {
            _0x3a7931 += $(this).outerHeight(true);
          }),
            _0x492fb6.find("> th").each(function () {
              $(this).css("top", _0x3a7931 + "px");
            }));
        });
      if ($parent.data("divIdRoot") != "B.I.") {
        if (_0x49a604.length == 0x1) {
          var _0x556557 =
            'th.grid-column-header:not([valor="Y291bnRMaW5oYQ=="]):not([valor="cm93RGF0YQ=="]):not([valor="YWRkQ29sdW5h"])';
          $this
            .find("> thead")
            .sortable({
              items: _0x556557,
              axis: "x",
              helper: "clone",
              opacity: 0.7,
              distance: 0x3,
              start: function (_0x2ad310, _0x10234b) {
                $(".colunaAcoes").remove();
                var _0x3cc11b = $(_0x10234b.item[0x0]);
                _0x3cc11b.hasClass("grid-column-freeze") &&
                  _0x3cc11b.css("left", "");
                ($(_0x10234b.helper[0x0]).css("top", "auto"),
                  $(this).closest("table.grid").css("table-layout", "auto"));
                var _0x162eb6 = $(_0x10234b.item.context).attr("valor"),
                  _0x21d2c3 = 0x0,
                  _0x374b78 = 0x0;
                ($(this)
                  .find("th")
                  .each(function () {
                    if (
                      !$(this).attr("valor") ||
                      $(this).attr("valor") == "Y291bnRMaW5oYQ=="
                    )
                      return;
                    _0x374b78++;
                    if (_0x162eb6 == $(this).attr("valor"))
                      return ((_0x21d2c3 = _0x374b78), false);
                  }),
                  (g.ordemAlteracao = false),
                  (g.colunaOrdemOriginal = _0x21d2c3));
              },
              stop: function (_0x14b965, _0x1dc4ad) {
                ($(this)
                  .find(_0x556557)
                  .each(function () {
                    $(this).css("width", $(this).width() + "px");
                  }),
                  $(_0x1dc4ad.item[0x0])
                    .closest("table.grid")
                    .css("table-layout", "fixed"));
                if (g.ordemAlteracao) {
                  var _0x24961f = $(_0x1dc4ad.item.context).attr("valor");
                  if (
                    !_0x24961f ||
                    _0x24961f == "Y291bnRMaW5oYQ==" ||
                    $(_0x1dc4ad.item.context).data("disable-order")
                  )
                    return;
                  var _0x96ae58 = 0x0,
                    _0x35108a = 0x0;
                  $(this)
                    .find("th")
                    .each(function () {
                      if (
                        !$(this).attr("valor") ||
                        $(this).attr("valor") == "Y291bnRMaW5oYQ=="
                      )
                        return;
                      (_0x35108a++,
                        _0x24961f == $(this).attr("valor") &&
                          (_0x96ae58 = _0x35108a));
                    });
                  var _0x4c72b7 = $(".content:visible:last");
                  (_0x4c72b7.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
                    (_0x4c72b7 = _0x4c72b7.find(".ui-tabs-panel:visible:last")),
                    $.getJSON(
                      "/sistema/personalizacaoGrid/form_func-ajax/personalizarGrid/?ajax=true&colunaValor=" +
                        encodeURIComponent(_0x24961f) +
                        "&grid=" +
                        encodeURIComponent(_0x4c72b7.data("md5")) +
                        "&opcao=" +
                        encodeURIComponent("colunaOrdem") +
                        "&opcaoValor=" +
                        encodeURIComponent(_0x96ae58) +
                        "&colunaOrdemAntigo=" +
                        encodeURIComponent(g.colunaOrdemOriginal) +
                        "&callback=?",
                      function (_0x13c87f) {
                        returnfalse;
                      },
                    ));
                }
              },
              change: function (_0x3c391d, _0x59550a) {
                g.ordemAlteracao = true;
              },
            })
            .disableSelection();
        }
        $this.find("> thead th.grid-column-header").each(function () {
          var _0x2bc98d = $(this),
            _0x22acce = {
              handles: "e",
              start: function (_0x30ee14, _0x6ca142) {
                $(".colunaAcoes").remove();
              },
              stop: function (_0xe02c9a, _0x557ad7) {
                var _0x3510fe = $(".content:visible:last");
                _0x3510fe.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
                  (_0x3510fe = _0x3510fe.find(".ui-tabs-panel:visible:last"));
                var _0x20efb7 =
                  typeof _0x3510fe.data("pesquisaGridId") == "undefined"
                    ? ""
                    : _0x3510fe.data("pesquisaGridId");
                $.ajax({
                  url: "/sistema/personalizacaoGrid/form_func-ajax/width/",
                  dataType: "jsonp",
                  type: "post",
                  data:
                    "ajax=true&coluna=" +
                    encodeURIComponent($(this).attr("valor")) +
                    "&width=" +
                    encodeURIComponent($(this).width()) +
                    "&pesquisaGridId=" +
                    encodeURIComponent(_0x20efb7) +
                    "&grid=" +
                    encodeURIComponent(_0x3510fe.data("href")) +
                    _0x3510fe.data("vars"),
                });
              },
            };
          (_0x49a604.length > 0x1 &&
            (_0x22acce.alsoResize = _0x2bc98d
              .closest("thead")
              .find("tr:first th")
              .eq(_0x2bc98d.index())),
            _0x2bc98d.resizable(_0x22acce));
        });
      }
      ($div.unbind("scroll"),
        $div.bind("scroll", function () {
          ($(".colunaAcoes").remove(),
            $(".popover")
              .prev()
              .popover("destroy")
              .data("clicked", false)
              .data("show", false));
          if ($(this).find(".grid").hasClass("noScrollLoading")) returntrue;
          var _0x164304 = $(this).find("table").height() - $(this).height();
          if (
            $(this).scrollTop() > 0x0 &&
            $(this).scrollTop() + 0x64 > _0x164304
          ) {
            var _0x4496a3 = false;
            $(this).scrollTop() > _0x164304 && (_0x4496a3 = true);
            var _0x3886c3 = gridPegarDivId($(this));
            ($(this).unbind("scroll"),
              $(
                '<tr class="trLoading"><td colspan="300" style="text-align: left; padding-left: 128px;">Carregando mais resultados! Por favor, aguarde ...</td></tr>',
              ).appendTo($(this).find("table.grid > tbody:last")),
              _0x4496a3 && $(this).scrollTop($(this).find("table").height()),
              divRefresh(
                _0x3886c3,
                false,
                function (_0x4aea8f, _0x5bc6ab) {
                  const _0x55ec92 = _0x5bc6ab.find(".divToScroll");
                  _0x55ec92
                    .find("table.grid > tbody:last tr.trLoading")
                    .remove();
                  var _0x198d17 = false,
                    _0x32690d;
                  (_0x55ec92.find("table.grid thead.theadagrupamento").length >
                  0x0
                    ? ((_0x198d17 = true),
                      (_0x32690d = $(_0x4aea8f).find("table.grid > *")))
                    : (_0x32690d = $(_0x4aea8f).find(
                        "table.grid > tbody:first > *",
                      )),
                    $(_0x4aea8f).find("table.grid > tbody > *").size() > 0x0
                      ? (_0x198d17
                          ? _0x32690d.appendTo(_0x55ec92.find("table.grid"))
                          : _0x32690d.appendTo(
                              _0x55ec92.find("table.grid > tbody"),
                            ),
                        $("<div></div>")
                          .css({
                            display: "none",
                            width: 0x0,
                            height: 0x0,
                            position: "fixed",
                          })
                          .html(_0x4aea8f)
                          .appendTo("body")
                          .remove(),
                        corrigeGrid(_0x3886c3))
                      : _0x55ec92.bind("scroll", function () {
                          $(".popover")
                            .prev()
                            .popover("destroy")
                            .data("clicked", false)
                            .data("show", false);
                        }));
                },
                false,
              ));
          }
        }),
        $(this).closest("table.grid").css("table-layout", "fixed"));
      typeof gridTheadCallback != "undefined" && gridTheadCallback();
      var _0x5e043b = 0x0;
      ($('.gridFiltro input[type="submit"]:visible').each(function () {
        _0x5e043b =
          $(this).attr("tabindex") > _0x5e043b
            ? $(this).attr("tabindex")
            : _0x5e043b;
      }),
        $parent.find(".gridFiltro .formInput:visible").each(function () {
          (_0x5e043b++, $(this).attr("tabindex", _0x5e043b));
        }),
        $parent.find('.gridFiltro input[type="submit"]').each(function () {
          (_0x5e043b++, $(this).attr("tabindex", _0x5e043b));
        }));
    });
  }, 0x64);
}
function menuId(_0x1ddb31) {
  return (
    !isset(menuUniques[_0x1ddb31]) && (menuUniques[_0x1ddb31] = unique()),
    menuUniques[_0x1ddb31]
  );
}
function divRefresh(
  _0x175ac0,
  _0x4f3e49,
  _0x38040f,
  _0x478f7f,
  _0xd6cc3,
  _0x3b6f90,
  _0x2274a7,
) {
  var _0x4f3e49 = typeof _0x4f3e49 == "undefined" ? false : _0x4f3e49,
    _0x38040f = typeof _0x38040f == "undefined" ? false : _0x38040f,
    _0x478f7f = typeof _0x478f7f == "undefined" ? true : _0x478f7f,
    _0xd6cc3 = typeof _0xd6cc3 == "undefined" ? false : _0xd6cc3,
    _0x3b6f90 = typeof _0x3b6f90 == "undefined" ? false : _0x3b6f90,
    _0x2274a7 = typeof _0x2274a7 == "undefined" ? true : _0x2274a7,
    _0x2f5bf3 = $("#" + _0x175ac0);
  if (_0x2f5bf3.size() == 0x0) returnfalse;
  ($(".colunaAcoes").remove(),
    $(".list-options-form").remove(),
    abortarRequisicoesAjax(_0x2f5bf3));
  _0x2f5bf3.hasClass("ui-dialog-content") && (_0x4f3e49 = false);
  _0x478f7f &&
    _0x478f7f != 0x2 &&
    (_0x478f7f == "manual"
      ? _0x2f5bf3.append(
          $(
            '<div class="loading-indicator-overlay-manual" style="width: 100%; height: 100%; position: absolute; left: 0; top: 0;"><div class="loading-indicator-manual" style="margin: auto; height: 100%;"></div></div>',
          ),
        )
      : (_0x2f5bf3.showLoading(), _0x2f5bf3.data("loading", 0x0)));
  var _0x1a510f =
    "ajax=true&divIdRoot=" +
    _0x2f5bf3.data("divIdRoot") +
    "&divIdParent=" +
    _0x2f5bf3.data("divIdParent") +
    "&divIdParentChain=" +
    _0x2f5bf3.data("divIdParentChain") +
    "&divId=" +
    _0x2f5bf3.data("divId") +
    "&divIdChain=" +
    _0x2f5bf3.data("divIdChain") +
    _0x2f5bf3.data("vars");
  _0x2f5bf3.hasClass("ui-dialog-content") &&
    (_0x1a510f =
      "ajax=true&divIdRoot=" +
      _0x2f5bf3.data("janelaIdRoot") +
      "&divIdParent=" +
      _0x2f5bf3.data("janelaIdParent") +
      "&divIdParentChain=" +
      _0x2f5bf3.data("janelaIdParentChain") +
      "&divId=" +
      _0x2f5bf3.data("janelaId") +
      _0x2f5bf3.data("janelaVars"));
  _0xd6cc3 &&
    (_0x1a510f = _0x1a510f + "&linhaGridId=" + encodeURIComponent(_0xd6cc3));
  if (!_0xd6cc3 && !_0x478f7f) {
    var _0x2e2c9e = isempty(_0x2f5bf3.data("loading"))
      ? 0x0
      : _0x2f5bf3.data("loading");
    (_0x2f5bf3.data("loading", _0x2e2c9e + 0x1),
      (_0x1a510f += "&loading=" + (_0x2e2c9e + 0x1)));
  }
  if (_0xd6cc3 && _0x2274a7) {
    if (!_0x3b6f90)
      var _0x74a0f6 = _0x2f5bf3.find('tr[id="' + _0xd6cc3 + '"]:first');
    else
      var _0x74a0f6 = _0x2f5bf3.find(
        'tbody > tr[id*="' + _0x3b6f90 + "-" + _0xd6cc3 + '"]:first',
      );
    _0x74a0f6.addClass("gridLinhaRefresh");
  }
  var _0x2cd90a = $.ajax({
    url: _0x2f5bf3.hasClass("ui-dialog-content")
      ? _0x2f5bf3.data("janelaHref")
      : _0x2f5bf3.data("href"),
    dataType: "html",
    data: _0x1a510f,
    type: "post",
    success: function (_0x386fdf) {
      _0x478f7f &&
        _0x478f7f != 0x2 &&
        _0x478f7f != "manual" &&
        _0x2f5bf3.hideLoading();
      if (_0x478f7f && _0x478f7f != "pular") {
        const _0xe2a85 = _0x2f5bf3.data("maximizado");
        (_0xe2a85 && minimizarGrid(_0x175ac0),
          _0x2f5bf3.html(_0x386fdf),
          reloadUI(_0x175ac0),
          corrigeConteudo(_0x175ac0),
          _0x4f3e49 && _0x2f5bf3.find("> .buttonsBottom").remove(),
          corrigeGrid(_0x175ac0),
          _0xe2a85 &&
            setTimeout(function () {
              (_0x2f5bf3
                .find(".gridTheadMaximizar")
                .find("span")
                .removeClass("ui-icon-triangle-1-n")
                .addClass("ui-icon-triangle-1-s"),
                maximizarGrid(_0x175ac0),
                $("#gridMaxOverlayReload").remove());
            }, 0xc8));
      }
      if (_0xd6cc3) {
        var _0x786fe8 = $("<div></div>").html(_0x386fdf);
        _0x786fe8.find(".gridFiltroAvancado > table").remove();
        _0x2274a7 &&
          _0x786fe8
            .find("tbody > tr:not(.grid-group-divisor):first")
            .addClass("gridLinhaRefresh");
        if (!_0x3b6f90)
          var _0x42cef0 = _0x2f5bf3.find('tr[id="' + _0xd6cc3 + '"]:first');
        else
          var _0x42cef0 = _0x2f5bf3.find(
            'tbody > tr[id*="' + _0x3b6f90 + "-" + _0xd6cc3 + '"]:first',
          );
        _0x42cef0.hasClass("linhaSelecionada") &&
          _0x786fe8
            .find("tbody > tr:not(.grid-group-divisor):first")
            .addClass("linhaSelecionada");
        _0x786fe8.find(".gridAgrupado").length > 0x0 &&
          (_0x42cef0.find("td[rowspan]").length > 0x0
            ? _0x786fe8
                .find("td[rowspan=1]")
                .attr(
                  "rowspan",
                  _0x42cef0.find("td[rowspan]:first").attr("rowspan"),
                )
            : _0x786fe8.find("td[rowspan=1]").remove());
        if (_0x42cef0.size()) {
          if (
            _0x786fe8.find("tbody > tr:not(.grid-group-divisor):first").size() >
            0x0
          ) {
            var _0x2985e3 = _0x42cef0
                .closest(".grid")
                .find("thead.theadnormal > tr:last"),
              _0x53e943 = _0x786fe8.find("thead.theadnormal > tr:last"),
              _0x5666d0 = _0x786fe8.find(
                "tbody > tr:not(.grid-group-divisor):first",
              );
            _0x2985e3.find("> th").each(function () {
              var _0x164b9b = $(this).index();
              $(this).attr("valor") !==
              _0x53e943.find("th:eq(" + _0x164b9b + ")").attr("valor")
                ? (_0x53e943
                    .find("th:eq(" + _0x164b9b + ")")
                    .before($(this).clone()),
                  _0x42cef0.find("td:eq(" + _0x164b9b + ")").html("-"),
                  _0x5666d0
                    .find("td:eq(" + _0x164b9b + ")")
                    .before(_0x42cef0.find("td:eq(" + _0x164b9b + ")").clone()))
                : $(this).hasClass("colunaNaoAtualizar") &&
                  _0x5666d0
                    .find("td:eq(" + _0x164b9b + ")")
                    .html(_0x42cef0.find("td:eq(" + _0x164b9b + ")").html());
            });
            var _0x6b1f40 = _0x42cef0.find("td.colunaSelecionada"),
              _0x248056 = _0x6b1f40.data("background"),
              _0x35cea3 = _0x42cef0.find("td").index(_0x6b1f40),
              _0x1ded1a = _0x42cef0.hasClass("linhaModoCelula");
            (_0x42cef0.find("> td").each(function () {
              var _0x272542 = $(this).index(),
                _0x3ab5e5 = $(this).css("left");
              _0x3ab5e5 != "auto" &&
                _0x5666d0
                  .find("td:eq(" + _0x272542 + ")")
                  .css("left", $(this).css("left"));
            }),
              _0x42cef0.html(
                _0x786fe8
                  .find("tbody > tr:not(.grid-group-divisor):first")
                  .html(),
              ),
              _0x42cef0
                .find("td:first span.linhaCount")
                .html(_0x42cef0.attr("count")));
            if (_0x1ded1a && linhaSelecionadaModoCelula) {
              _0x42cef0.addClass("linhaModoCelula");
              var _0x53c220 = _0x42cef0.find("td:eq(" + _0x35cea3 + ")");
              (_0x53c220.data("background", _0x248056),
                _0x53c220
                  .addClass("colunaSelecionada")
                  .css("background", colunaSelecionadaBackgroud));
            }
            (_0x786fe8.remove(),
              _0x2f5bf3.find(".grid tbody tr").unbind("click"));
            var _0x786fe8 = $("<div></div>").html(_0x386fdf);
            ultimoCampoTab.length > 0x0 &&
              ultimoCampoTab[0x0] == _0x3b6f90 + "-" + _0xd6cc3 &&
              _0x42cef0
                .find("td:eq(" + ultimoCampoTab[0x1] + ")")
                .trigger("click");
          } else {
            _0x42cef0.remove();
            var _0x434e88 = 0x0;
            _0x2f5bf3.find(".grid tr[count]").each(function () {
              (_0x434e88++,
                $(this).attr("count", _0x434e88),
                $(this).find("td:first span.linhaCount").html(_0x434e88));
            });
          }
        } else {
          var _0x1f6c1b = _0x786fe8
              .find("tbody > tr:not(.grid-group-divisor):first")
              .attr("id"),
            _0xde8a9e = _0x2f5bf3.find('tr[id*="' + _0x1f6c1b + '"]:first');
          !_0xde8a9e.size() &&
            _0x786fe8
              .find("tbody > tr:not(.grid-group-divisor):first")
              .prependTo(_0x2f5bf3.find(".grid tbody"));
          (_0x786fe8.remove(),
            _0x2f5bf3.find(".grid tbody tr").unbind("click"));
          var _0x786fe8 = $("<div></div>").html(_0x386fdf),
            _0x434e88 = 0x0;
          _0x2f5bf3.find(".grid tr[count]").each(function () {
            (_0x434e88++,
              $(this).attr("count", _0x434e88),
              $(this).find("td:first span.linhaCount").html(_0x434e88));
          });
        }
        (corrigeGrid(_0x175ac0), _0x786fe8.remove());
      }
      return (_0x38040f && _0x38040f(_0x386fdf, _0x2f5bf3), false);
    },
  });
  (typeof requisicoesAjax[_0x175ac0] == "undefined" &&
    (requisicoesAjax[_0x175ac0] = []),
    requisicoesAjax[_0x175ac0].push(_0x2cd90a));
}
function divSetarAcaoId(_0x3656d8, _0x1fca32, _0x24ddc5) {
  var _0x24ddc5 = typeof _0x24ddc5 == "undefined" ? "acaoId" : _0x24ddc5,
    _0x226862 = $("#" + _0x3656d8),
    _0x1b5aad = _0x226862.data("vars"),
    _0x17e12d = false;
  !_0x1b5aad &&
    ((_0x1b5aad = _0x226862.data("janelaVars")),
    _0x1b5aad && (_0x17e12d = true));
  typeof _0x1b5aad == "undefined" && (_0x1b5aad = "");
  var _0xf2fb3c = "";
  if (_0x1b5aad.match(_0x24ddc5 + "=")) {
    var _0x85d54c = _0x1b5aad.split("&");
    ($.each(_0x85d54c, function (_0xa871cc, _0x381ff9) {
      if (_0xa871cc != 0x0) {
        if (_0x381ff9.match(_0x24ddc5 + "=")) {
          var _0x1c21e6 = _0x381ff9.split("=");
          _0xf2fb3c = _0xf2fb3c + "&" + _0x1c21e6[0x0] + "=" + _0x1fca32;
        } else _0xf2fb3c = _0xf2fb3c + "&" + _0x381ff9;
      }
    }),
      _0x17e12d
        ? _0x226862.data({ janelaVars: _0xf2fb3c })
        : _0x226862.data({ vars: _0xf2fb3c }));
  } else
    _0x17e12d
      ? _0x226862.data({
          janelaVars: _0x1b5aad + "&" + _0x24ddc5 + "=" + _0x1fca32,
        })
      : _0x226862.data({ vars: _0x1b5aad + "&" + _0x24ddc5 + "=" + _0x1fca32 });
  returntrue;
}
function moveBtnsToContent(_0x239354) {
  var _0x25133d = $("#" + _0x239354);
  if (
    _0x25133d.closest(".content").parent().find("> .buttonsBottom").size() > 0x0
  ) {
    if (
      typeof _0x25133d
        .closest(".content")
        .parent()
        .find("> .buttonsBottom")
        .attr("id") == "undefined"
    )
      returnfalse;
    if (
      _0x25133d.find(".buttonsBottom").attr("id") !=
      _0x25133d.closest(".content").parent().find("> .buttonsBottom").attr("id")
    ) {
      var _0x100b7e = _0x25133d
          .closest(".content")
          .parent()
          .find("> .buttonsBottom"),
        _0x2c7bcb = _0x100b7e.attr("id").replace(/^btns\-/, "");
      _0x100b7e.appendTo($("#" + _0x2c7bcb));
    }
    _0x25133d.closest(".content").parent().find("> .buttonsBottom").remove();
  }
  if (_0x25133d.find(".buttonsBottom").size() > 0x0) {
    if (
      _0x25133d.closest(".content").size() == 0x0 ||
      typeof _0x25133d.find(".buttonsBottom").attr("id") == "undefined"
    )
      returnfalse;
    (_0x25133d
      .find(".buttonsBottom")
      .appendTo(_0x25133d.closest(".content").parent()),
      _0x25133d.find(".buttonsBottom").remove());
  }
  _0x25133d
    .closest(".ui-dialog")
    .find(".content")
    .each(function (_0x45408c, _0x574691) {
      corrigeConteudo($(this).attr("id"));
    });
}
function formSalvarEstado(_0x1c31d9) {
  var _0x233305 = $("#" + _0x1c31d9);
  _0x233305.find(".itemForm").each(function () {
    $(this).data("itemFormVal", $(this).val());
  });
}
function formCarregarEstado(_0x281fa7) {
  var _0x30460f = $("#" + _0x281fa7);
  (_0x30460f.find(".itemForm").each(function () {
    $(this).attr("type") != "button" &&
      $(this).val($(this).data("itemFormVal"));
  }),
    _0x30460f.find(".itemForm:visible:first").focus());
}
function gExcluirLinha(
  _0x3700b5,
  _0x186f06,
  _0x15b220,
  _0x273af4,
  _0x217470,
  _0x5cade5,
) {
  var _0x57e62b = {
    Deletar: function () {
      ($("#" + _0x15b220).showLoading(),
        $.getJSON(
          "/sistema/funcoes/excluirLinha/excluir/?ajax=true&pk=" +
            encodeURIComponent(_0x3700b5) +
            "&pkValor=" +
            encodeURIComponent(_0x186f06) +
            "&divId=" +
            encodeURIComponent(_0x15b220) +
            "&campoParent=" +
            encodeURIComponent(_0x273af4) +
            "&valorParent=" +
            encodeURIComponent(_0x217470) +
            "&divReload=" +
            encodeURIComponent(_0x5cade5) +
            "&callback=?",
          function (_0x27f104) {
            $("#" + _0x15b220).hideLoading();
            if (_0x27f104.code)
              (removerLinhaGrid(_0x15b220, _0x27f104.data),
                divRefresh(_0x5cade5),
                reloadUI(_0x15b220));
            else {
              var _0x8212a2 = {
                Fechar: function () {
                  $(this).dialog("destroy").remove();
                },
              };
              dialogConfirm("Erro ao Excluir Item", _0x27f104.data, _0x8212a2);
            }
          },
        ),
        $(this).dialog("destroy").remove());
    },
    Cancelar: function () {
      $(this).dialog("destroy").remove();
    },
  };
  dialogConfirm(
    "Deseja Remover?",
    "Atenção! Você deseja realmente remover este item?",
    _0x57e62b,
  );
}
function gEditarCampo(
  _0x577ed0,
  _0x434320,
  _0x38445c,
  _0x642118,
  _0x1494d3,
  _0x2bcfd8,
  _0x143811,
  _0x3c196a,
  _0x517768,
) {
  var _0x38445c = typeof _0x38445c == "undefined" ? "" : _0x38445c,
    _0x642118 = typeof _0x642118 == "undefined" ? "" : _0x642118,
    _0x2bcfd8 = typeof _0x2bcfd8 == "undefined" ? "" : _0x2bcfd8,
    _0x1494d3 = typeof _0x1494d3 == "undefined" ? "" : _0x1494d3,
    _0x143811 = typeof _0x143811 == "undefined" ? true : _0x143811,
    _0x3c196a = typeof _0x3c196a == "undefined" ? false : _0x3c196a,
    _0x517768 = typeof _0x517768 == "undefined" ? true : _0x517768,
    _0x4b6e61 = _0x434320.closest("tr").attr("id"),
    _0x187a2e = _0x434320.data("pk");
  _0x187a2e &&
    (_0x4b6e61 = _0x4b6e61 ? _0x4b6e61 + "|" + _0x187a2e : _0x187a2e);
  linhaSelecionadaModoCelula && selecionarCelula(_0x434320);
  if (!isempty(_0x1494d3))
    return (
      $.getJSON(
        "/sistema/funcoes/editarCampo/gGeraSelect/?ajax=true&gGeraSelect=" +
          encodeURIComponent(_0x1494d3) +
          "&callback=?",
        function (_0x41b8aa) {
          _0x434320.prop("nodeName") != "TD" &&
            (_0x434320 = _0x434320.closest("td"));
          (_0x434320.css("width", "auto"), _0x434320.width(_0x434320.width()));
          typeof _0x434320.find("span:eq(0)").data("valorAtual") ==
            "undefined" &&
            (_0x434320
              .find("span:eq(0)")
              .data(
                "valorAtual",
                _0x434320.find("span:eq(0)").find("div").text(),
              ),
            _0x434320.find("span:eq(0)").find("div").remove());
          var _0x5f5801 = _0x434320.find("span:eq(0)").html(),
            _0x48355d = _0x434320.find("span:eq(0)").data("valorAtual");
          _0x434320.html("");
          if (_0x434320.find("select").size() == 0x0) {
            var _0xf7dd33 = $("<select>")
              .html(_0x41b8aa.data)
              .appendTo(_0x434320)
              .bind("click", function (_0x3ddce8) {
                _0x3ddce8.stopPropagation();
              })
              .bind("change", function (_0x599ca3) {
                if (typeof _0x599ca3.originalEvent == "undefined") returntrue;
                var _0x3581e5 = $(this),
                  _0x119dee = _0x3581e5.closest("td"),
                  _0x3de354 = _0x3581e5.closest(".content");
                _0x517768 && _0x3de354.showLoading();
                var _0x18800f = _0x119dee.data("campo-original");
                ($.getJSON(
                  "/sistema/funcoes/editarCampo/editar/?ajax=true&chave=" +
                    encodeURIComponent(_0x4b6e61) +
                    "&campo=" +
                    encodeURIComponent(_0x577ed0) +
                    "&valor=" +
                    encodeURIComponent(_0x3581e5.val()) +
                    "&tipo=" +
                    encodeURIComponent(_0x642118) +
                    "&insert=" +
                    encodeURIComponent(_0x3c196a) +
                    "&callback=?",
                  function (_0x3f70da) {
                    _0x517768 && _0x3de354.hideLoading();
                    if (_0x3f70da.code) {
                      var _0x3b0368 = _0x3581e5.data("oldValue"),
                        _0x586c0e = _0x3581e5.val();
                      (_0x119dee.html(""),
                        $("<span></span>")
                          .html(_0x3581e5.find("option:selected").text())
                          .appendTo(_0x119dee)
                          .data("valorAtual", _0x586c0e));
                      var _0x32869d = false;
                      _0x119dee.find("label:visible").each(function () {
                        if (_0x32869d) returnfalse;
                        var _0x37e674 = $(this).find(
                          "input:visible,textarea:visible,select:visible",
                        );
                        _0x37e674.size() != 0x0 &&
                          ((_0x32869d = true),
                          _0x37e674
                            .closest(".ui-tabs-panel")
                            .data("divIdRoot") != "B.I." &&
                            !_0x37e674.hasClass("campo_input_data") &&
                            _0x37e674.focus());
                      });
                      if (!isempty(_0x38445c) && _0x38445c !== "callback")
                        window[_0x38445c](
                          _0x4b6e61,
                          _0x577ed0,
                          _0x3b0368,
                          _0x586c0e,
                        );
                      else {
                        if (_0x38445c == "callback") {
                          const _0x21a0a6 = gridPegarDivId(_0x119dee);
                          $.ajax({
                            url: "/sistema/personalizacaoGrid/form_func-ajax/colunaEditarCallback/",
                            dataType: "html",
                            type: "POST",
                            data: {
                              ajax: true,
                              divId: _0x21a0a6,
                              divIdChain: $("#" + _0x21a0a6).data("divIdChain"),
                              divIdParent: $("#" + _0x21a0a6).data(
                                "divIdParent",
                              ),
                              divIdParentChain: $("#" + _0x21a0a6).data(
                                "divIdParentChain",
                              ),
                              gridId: $("#" + _0x21a0a6).data("md5"),
                              campoId: _0x18800f ? _0x18800f : _0x577ed0,
                              campoAtual: _0x586c0e,
                              campoAtualAntigo: _0x3b0368,
                              todosCampos: _0x119dee
                                .closest(".linha")
                                .attr("todosCampos"),
                            },
                            success: function (_0x559e50) {
                              return (
                                $("#" + _0x21a0a6).append(_0x559e50),
                                false
                              );
                            },
                          });
                        }
                      }
                    } else {
                      divRefresh(
                        gridPegarDivId(_0x119dee),
                        true,
                        false,
                        false,
                        _0x4b6e61,
                        false,
                        false,
                      );
                      var _0x28a0f8 = {
                        Fechar: function () {
                          $(this).dialog("destroy").remove();
                        },
                      };
                      dialogConfirm(
                        "Erro ao Editar Campo",
                        _0x3f70da.data,
                        _0x28a0f8,
                      );
                    }
                  },
                ),
                  _0x599ca3.stopPropagation());
              });
            (_0x143811 && _0xf7dd33.val(_0x48355d),
              corrigeGrid(_0x434320.closest(".content").attr("id")),
              setTimeout(function () {
                _0xf7dd33.focus();
              }, 0x64));
          }
          reloadUI(_0x434320.closest(".content").attr("id"), false);
        },
      ),
      false
    );
  var _0x5ebf03 = "",
    _0x46b5c0 = "",
    _0x5b1d9f = "";
  if (_0x642118.match("moeda")) {
    var _0x5d13c4 = _0x642118.split("-");
    ((_0x46b5c0 = "return(Corrige_Valor(event, this," + _0x5d13c4[0x1] + "))"),
      (_0x5ebf03 = "return(Verifica_Caracter(event, this))"));
  }
  _0x642118.match("tempo") && (_0x5b1d9f = "return(Formatar_Tempo(this))");
  if (_0x434320.find("input").size() == 0x0) {
    var _0x318567 = _0x434320.attr("editarRapido");
    (_0x434320.css("width", "auto"), _0x434320.width(_0x434320.width()));
    var _0x144827 = _0x434320.find("span:eq(0)").html();
    (_0x434320.html(""), (_0x25ae61 = "none"));
    if (_0x318567) var _0x25ae61 = "1px solid silver";
    var _0x480b54 = $("<input>")
      .addClass("input-editar-campo")
      .css({
        width: _0x434320.width() - 0xa + "px",
        "text-transform": "none",
        border: _0x25ae61,
      })
      .attr({
        type: "text",
        value: _0x144827,
        onchange: _0x46b5c0,
        onkeypress: _0x5ebf03,
        onkeyup: _0x5b1d9f,
        tabindex: 0xf423f,
      })
      .data({ oldValue: _0x144827 })
      .appendTo(_0x434320);
    _0x2bcfd8 == true && _0x480b54.css("text-transform", "uppercase");
    _0x642118.match("moeda") && _0x480b54.css("text-align", "right");
    if (_0x642118.match("dataHora")) _0x480b54.addClass("campo_input_datahora");
    else
      _0x642118.match("data") &&
        !_0x642118.match("dataHora") &&
        _0x480b54.addClass("campo_input_data");
    (reloadUI(_0x434320.closest(".content").attr("id"), false),
      corrigeGrid(_0x434320.closest(".content").attr("id")),
      _0x480b54
        .focus()
        .select()
        .bind("change", function (_0x3e95ff) {
          var _0x5b6095 = $(this);
          if (_0x5b6095.hasClass("campo_input_data"))
            (_0x5b6095.removeClass("campo_input_data"),
              _0x5b6095.trigger("focus"),
              _0x5b6095.trigger("blur"));
          else {
            if (_0x5b6095.hasClass("campo_input_datahora"))
              return (_0x5b6095.trigger("focus"), false);
          }
        })
        .bind("blur", function (_0x167aef) {
          var _0x3213fb = $(this),
            _0x5b48e7 = _0x3213fb.data("datetimepickerSubmit") === true;
          if (typeof _0x167aef.originalEvent == "undefined" && !_0x5b48e7)
            returntrue;
          _0x5b48e7 && _0x3213fb.removeData("datetimepickerSubmit");
          if (
            _0x3213fb.hasClass("campo_input_data") ||
            _0x3213fb.hasClass("campo_input_datahora")
          )
            returntrue;
          var _0x4bceb8 = _0x3213fb.closest("td"),
            _0x12457a = _0x3213fb.closest(".content"),
            _0x1392de = _0x4bceb8.data("campo-original");
          if (_0x3213fb.data("oldValue") == _0x3213fb.val())
            return (
              !_0x318567 &&
                (_0x4bceb8.html(""),
                $("<span></span>").html(_0x144827).appendTo(_0x4bceb8)),
              true
            );
          _0x517768 && _0x12457a.showLoading();
          if (prevenirColunaEditarDuplo) returntrue;
          ((prevenirColunaEditarDuplo = true),
            $.getJSON(
              "/sistema/funcoes/editarCampo/editar/?ajax=true&chave=" +
                encodeURIComponent(_0x4b6e61) +
                "&campo=" +
                encodeURIComponent(_0x577ed0) +
                "&valor=" +
                encodeURIComponent(_0x3213fb.val()) +
                "&tipo=" +
                encodeURIComponent(_0x642118) +
                "&uppercase=" +
                encodeURIComponent(_0x2bcfd8) +
                "&insert=" +
                encodeURIComponent(_0x3c196a) +
                "&callback=?",
              function (_0x50f5bc) {
                prevenirColunaEditarDuplo = false;
                _0x517768 && _0x12457a.hideLoading();
                if (_0x50f5bc.code) {
                  var _0x3ae8a8 = _0x3213fb.data("oldValue");
                  if (isset(_0x50f5bc.valor)) var _0x559e3f = _0x50f5bc.valor;
                  else var _0x559e3f = _0x3213fb.val();
                  _0x2bcfd8 == true && (_0x559e3f = _0x559e3f.toUpperCase());
                  !_0x318567 &&
                    (_0x4bceb8.html(""),
                    $("<span></span>").html(_0x559e3f).appendTo(_0x4bceb8));
                  corrigeGrid(_0x12457a.attr("id"));
                  if (!isempty(_0x38445c) && _0x38445c !== "callback")
                    window[_0x38445c](
                      _0x4b6e61,
                      _0x577ed0,
                      _0x3ae8a8,
                      _0x559e3f,
                    );
                  else {
                    if (_0x38445c == "callback") {
                      const _0x5bdf96 = gridPegarDivId(_0x4bceb8);
                      $.ajax({
                        url: "/sistema/personalizacaoGrid/form_func-ajax/colunaEditarCallback/",
                        dataType: "html",
                        type: "POST",
                        data: {
                          ajax: true,
                          divId: _0x5bdf96,
                          divIdChain: $("#" + _0x5bdf96).data("divIdChain"),
                          divIdParent: $("#" + _0x5bdf96).data("divIdParent"),
                          divIdParentChain: $("#" + _0x5bdf96).data(
                            "divIdParentChain",
                          ),
                          gridId: $("#" + _0x5bdf96).data("md5"),
                          campoId: _0x1392de ? _0x1392de : _0x577ed0,
                          campoAtual: _0x559e3f,
                          campoAtualAntigo: _0x3ae8a8,
                          todosCampos: _0x4bceb8
                            .closest(".linha")
                            .attr("todosCampos"),
                        },
                        success: function (_0x3a0513) {
                          return ($("#" + _0x5bdf96).append(_0x3a0513), false);
                        },
                      });
                    }
                  }
                } else {
                  divRefresh(
                    gridPegarDivId(_0x4bceb8),
                    true,
                    false,
                    false,
                    _0x4b6e61,
                    false,
                    false,
                  );
                  var _0x53439c = {
                    Fechar: function () {
                      $(this).dialog("destroy").remove();
                    },
                  };
                  dialogConfirm(
                    "Erro ao Editar Campo",
                    _0x50f5bc.data,
                    _0x53439c,
                  );
                }
              },
            ));
        }));
  }
  returnfalse;
}
function gEditarCampoRapido(
  _0x4dd1a5,
  _0xfb813c,
  _0x419b80,
  _0x2d0d62,
  _0x39ad01,
  _0x103738,
  _0x3049dd,
  _0x373819,
  _0x5f038b,
) {
  var _0x2d0d62 = typeof _0x2d0d62 == "undefined" ? "" : _0x2d0d62,
    _0x103738 = typeof _0x103738 == "undefined" ? "" : _0x103738,
    _0x373819 = typeof _0x373819 == "undefined" ? false : _0x373819,
    _0x41e277 = _0xfb813c.closest("tr").attr("id"),
    _0x5f038b = typeof _0x5f038b == "undefined" ? true : _0x5f038b,
    _0x3a5822 = "",
    _0x45e8b2 = "",
    _0x168245 = "";
  if (_0x2d0d62.match("moeda")) {
    var _0x27ac70 = _0x2d0d62.split("-");
    ((_0x45e8b2 = "return(Corrige_Valor(event, this," + _0x27ac70[0x1] + "))"),
      (_0x3a5822 = "return(Verifica_Caracter(event, this))"));
  }
  _0x2d0d62.match("tempo") && (_0x168245 = "return(Formatar_Tempo(this))");
  if (_0xfb813c.find("input").size() == 0x0) {
    (_0xfb813c.css("width", "auto"), _0xfb813c.width(_0xfb813c.width()));
    var _0x358d4f = _0xfb813c.find("span:eq(0)").html();
    _0xfb813c.html("");
    var _0x2085b3 = $("<input>")
      .addClass("input-editar-campo-rapido")
      .css({
        width: _0xfb813c.width() - 0xa + "px",
        "text-transform": "none",
        border: "1px solid silver",
      })
      .attr({
        type: "text",
        value: _0x358d4f,
        onchange: _0x45e8b2,
        onkeypress: _0x3a5822,
        onkeyup: _0x168245,
        tabindex: 0xf423f,
      })
      .data({ oldValue: _0x358d4f })
      .appendTo(_0xfb813c);
    _0x103738 == true && _0x2085b3.css("text-transform", "uppercase");
    _0x2d0d62.match("moeda") && _0x2085b3.css("text-align", "right");
    if (_0x2d0d62.match("dataHora")) _0x2085b3.addClass("campo_input_datahora");
    else _0x2d0d62.match("data") && _0x2085b3.addClass("campo_input_data");
    _0x2085b3
      .bind("change", function (_0x4444bf) {
        var _0x32dd68 = $(this);
        if (_0x32dd68.hasClass("campo_input_data"))
          (_0x32dd68.removeClass("campo_input_data"),
            _0x32dd68.trigger("focus"),
            _0x32dd68.trigger("blur"));
        else {
          if (_0x32dd68.hasClass("campo_input_datahora"))
            return (_0x32dd68.trigger("focus"), false);
        }
      })
      .bind("blur", function (_0x1e2f99) {
        var _0x4437bc = $(this),
          _0x1f0f3f = _0x4437bc.data("datetimepickerSubmit") === true;
        if (typeof _0x1e2f99.originalEvent == "undefined" && !_0x1f0f3f)
          returntrue;
        _0x1f0f3f && _0x4437bc.removeData("datetimepickerSubmit");
        if (
          _0x4437bc.hasClass("campo_input_data") ||
          _0x4437bc.hasClass("campo_input_datahora")
        )
          returntrue;
        var _0xba3e9d = _0x4437bc.closest("td"),
          _0x88294e = _0x4437bc.closest(".content");
        _0x5f038b && _0x88294e.showLoading();
        if (prevenirColunaEditarDuplo) returntrue;
        ((prevenirColunaEditarDuplo = true),
          $.getJSON(
            "/sistema/funcoes/editarCampo/editar/?ajax=true&chave=" +
              encodeURIComponent(_0x41e277) +
              "&campo=" +
              encodeURIComponent(_0x4dd1a5) +
              "&valor=" +
              encodeURIComponent(_0x4437bc.val()) +
              "&tipo=" +
              encodeURIComponent(_0x2d0d62) +
              "&uppercase=" +
              encodeURIComponent(_0x103738) +
              "&insert=" +
              encodeURIComponent(_0x373819) +
              "&callback=?",
            function (_0x4d2b21) {
              prevenirColunaEditarDuplo = false;
              _0x5f038b && _0x88294e.hideLoading();
              if (!_0x4d2b21.code) {
                ((campoTabela = _0x41e277.split("-")),
                  divRefresh(
                    gridPegarDivId(_0xba3e9d),
                    true,
                    false,
                    false,
                    campoTabela[0x1],
                    campoTabela[0x0],
                    false,
                  ));
                var _0x8c20c9 = {
                  Fechar: function () {
                    $(this).dialog("destroy").remove();
                  },
                };
                dialogConfirm(
                  "Erro ao Editar Campo",
                  _0x4d2b21.data,
                  _0x8c20c9,
                );
              }
            },
          ));
      });
  }
  returnfalse;
}
function gEditarColuna(
  _0x14beb7,
  _0x2d5baf,
  _0x31ac32,
  _0x28233f,
  _0x45795f,
  _0x3b0af3,
) {
  var _0x31ac32 = typeof _0x31ac32 == "undefined" ? "" : _0x31ac32,
    _0x28233f = typeof _0x28233f == "undefined" ? "" : _0x28233f,
    _0x45795f = typeof _0x45795f == "undefined" ? "" : _0x45795f,
    _0x4f544e = $(".divToScroll:visible:last").attr("gridsql"),
    _0x464fdf = gridPegarDivId(_0x14beb7),
    _0x464fdf = gridPegarDivId($(".divToScroll:visible:last")),
    _0x4bed04 = $("#" + _0x464fdf);
  $(".colunaAcoes").remove();
  if (_0x31ac32 == "form")
    abrirJanela(
      false,
      _0x4bed04.data("divIdRoot"),
      _0x4bed04.data("divId"),
      unique(),
      "",
      "Edição de todos campos da coluna",
      _0x28233f[0x0],
      "&campo=" +
        encodeURIComponent(_0x2d5baf) +
        "&tipo=" +
        encodeURIComponent(_0x31ac32) +
        "&sql=" +
        encodeURIComponent(_0x4f544e) +
        "&triggerPHP=" +
        encodeURIComponent(_0x3b0af3),
      [_0x28233f[0x1], _0x28233f[0x2]],
    );
  else {
    if (_0x31ac32 == "flag") {
      var _0xcb22a3 = _0x28233f.split("|"),
        _0x991cb0 = {
          "Marcar Todos": function () {
            ($("#" + _0x464fdf).showLoading(),
              $.ajax({
                url: "/sistema/funcoes/editarColuna/update/",
                type: "POST",
                dataType: "jsonp",
                data: {
                  ajax: true,
                  campo: encodeURIComponent(_0x2d5baf),
                  valor: encodeURIComponent(_0xcb22a3[0x0]),
                  sql: encodeURIComponent(_0x4f544e),
                  triggerPHP: encodeURIComponent(_0x3b0af3),
                  operacao: "valor",
                  tipo: encodeURIComponent(_0x31ac32),
                  extra: encodeURIComponent(_0x28233f),
                  usuario: encodeURIComponent(_0x45795f),
                },
              }).done(function (_0x3909ba, _0xe79a8a, _0x525772) {
                ($("#" + _0x464fdf).hideLoading(),
                  _0x3909ba.code
                    ? divRefresh(_0x464fdf)
                    : dialogConfirm("Erro!", _0x3909ba.data));
              }),
              $(this).dialog("destroy").remove());
          },
          "Desmarcar Todos": function () {
            ($("#" + _0x464fdf).showLoading(),
              $.ajax({
                url: "/sistema/funcoes/editarColuna/update/",
                type: "POST",
                dataType: "jsonp",
                data: {
                  ajax: true,
                  campo: encodeURIComponent(_0x2d5baf),
                  valor: encodeURIComponent(_0xcb22a3[0x1]),
                  sql: encodeURIComponent(_0x4f544e),
                  triggerPHP: encodeURIComponent(_0x3b0af3),
                  operacao: "valor",
                  tipo: encodeURIComponent(_0x31ac32),
                  extra: encodeURIComponent(_0x28233f),
                  usuario: encodeURIComponent(_0x45795f),
                },
              }).done(function (_0x4c4f44, _0x281b3b, _0x2e3bf0) {
                ($("#" + _0x464fdf).hideLoading(),
                  _0x4c4f44.code
                    ? divRefresh(_0x464fdf)
                    : dialogConfirm("Erro!", _0x4c4f44.data));
              }),
              $(this).dialog("destroy").remove());
          },
          Cancelar: function () {
            $(this).dialog("destroy").remove();
          },
        };
      dialogConfirm(
        "Marcar/Desmarcar Todos",
        "Você deseja marcar ou desmarcar todos?",
        _0x991cb0,
      );
    } else
      abrirJanela(
        false,
        _0x4bed04.data("divIdRoot"),
        _0x4bed04.data("divId"),
        unique(),
        "",
        "Edição de todos campos da coluna",
        "/sistema/funcoes/editarColuna/contentOpcoes/",
        "&campo=" +
          encodeURIComponent(_0x2d5baf) +
          "&tipo=" +
          encodeURIComponent(_0x31ac32) +
          "&extra=" +
          encodeURIComponent(_0x28233f) +
          "&sql=" +
          encodeURIComponent(_0x4f544e) +
          "&triggerPHP=" +
          encodeURIComponent(_0x3b0af3),
        [0x244, 0xc8],
      );
  }
  returnfalse;
}
function gRecarregaSelect(_0x37624f, _0x1cedaa, _0xc22fd) {
  var _0xd6e388 = $("#" + _0x37624f + " .itemForm-" + _0x1cedaa).val();
  ($("#" + _0x37624f).showLoading(),
    $.getJSON(
      "/sistema/funcoes/recarregarSelect/refresh/?ajax=true&array=" +
        encodeURIComponent(_0xc22fd) +
        "&callback=?",
      function (_0x239306) {
        $("#" + _0x37624f).hideLoading();
        if (_0x239306.code)
          ($("#" + _0x37624f + " .itemForm-" + _0x1cedaa).html(_0x239306.data),
            $("#" + _0x37624f + " .itemForm-" + _0x1cedaa).val(_0xd6e388));
        else {
          var _0x32f703 = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro!", _0x239306.data, _0x32f703);
        }
      },
    ));
}
function recarregarCampo(_0x311079, _0x576825, _0x1f26d3) {
  var _0x280ae9 = $("#" + _0x311079),
    _0x302733 = $("#" + _0x311079 + " .itemForm-" + _0x576825);
  ((_0x1f26d3 = typeof _0x1f26d3 == "undefined" ? "" : _0x1f26d3),
    $.ajax({
      url: _0x280ae9.data("href"),
      dataType: "html",
      data:
        "&ajax=true" +
        _0x280ae9.data("vars") +
        "&divId=" +
        _0x311079 +
        "&campoRefresh=" +
        encodeURIComponent(_0x576825) +
        _0x1f26d3 +
        "&campoRefreshVars=" +
        encodeURIComponent(_0x1f26d3),
      success: function (_0x1643ec) {
        (_0x302733.closest("div").html(_0x1643ec), _0x280ae9.form());
      },
    }));
}
function diagramaPrimeraJanela(_0x5468e7) {
  _0x5468e7.data("janelaId") &&
    ($("#" + _0x5468e7.data("janelaId")).showLoading(),
    diagramaLimparVars(),
    diagramaAbrirProg(
      _0x5468e7.data("janelaHref"),
      _0x5468e7.data("janelaTituloBtn"),
      "",
      0x0,
    ),
    docDiagramaDesenhar(),
    $("#" + _0x5468e7.data("janelaId")).hideLoading());
}
function diagramaLimparVars() {
  ((diagramaNodeDataArray = []),
    (diagramaLinkDataArray = []),
    (diagramaId = unique()),
    (diagramaProgs = []));
}
function diagramaAbrirProg(_0x4d6c99, _0xca2e7b, _0x1117a3, _0x429715) {
  $.ajax({
    url: "/sistema/funcoes/informacoes/parser/",
    dataType: "jsonp",
    data: "ajax=true&acaoId=" + encodeURIComponent(_0x4d6c99),
    async: false,
    success: function (_0x5d651d) {
      href = _0x4d6c99.split("/");
      !isset(href[0x1]) &&
        (href = ["", "vazio", "vazio", "vazio", "vazio", ""]);
      var _0x338158 = hrefCor(_0x4d6c99),
        _0x1a894d = href[0x3].toUpperCase(),
        _0x6e278b = "";
      (_0x4d6c99.indexOf("BOTAO|||") > -0x1 &&
        ((_0x338158 = "lime"), (_0x1a894d = "BOTÃO"), (_0x6e278b = "botao")),
        _0x4d6c99.indexOf("JS|||") > -0x1 &&
          ((_0x338158 = "gainsboro"), (_0x1a894d = "JAVASCRIPT")),
        jQuery.inArray(_0x4d6c99, diagramaProgs) < 0x0 &&
          (diagramaProgs.push(_0x4d6c99),
          diagramaNodeDataArray.push({
            key: _0x4d6c99,
            text: _0xca2e7b,
            tipo: _0x1a894d,
            personalizado: temPersonalizacao(_0x4d6c99),
            color: _0x338158,
            fig: "Ellipse",
            personalizar: "abrirConfiguracoes('" + _0x4d6c99 + "')",
            expandir:
              "diagramaLimparVars(); diagramaAbrirProg('" +
              _0x4d6c99 +
              "', '" +
              _0xca2e7b +
              "', '', 0); docDiagramaDesenhar();",
            category: _0x6e278b,
          })),
        (_0x1117a3 = _0x4d6c99),
        countObj(_0x5d651d.data.callbacks) > 0x0 &&
          (jQuery.inArray("Callbacks", diagramaProgs) < 0x0 &&
            (diagramaProgs.push("Callbacks"),
            diagramaNodeDataArray.push({ key: "Callbacks", isGroup: true }),
            diagramaLinkDataArray.push({ from: _0x1117a3, to: "Callbacks" })),
          $.each(_0x5d651d.data.callbacks, function (_0x52ab4d, _0x179cd4) {
            (jQuery.inArray(_0x179cd4[0x1] + "()", diagramaProgs) < 0x0 &&
              (diagramaProgs.push(_0x179cd4[0x1] + "()"),
              diagramaNodeDataArray.push({
                key: _0x179cd4[0x1] + "()",
                text: _0x179cd4[0x0],
                tipo: "CALLBACK",
                personalizado: temPersonalizacao(_0x179cd4[0x1]),
                color: "silver",
                personalizar:
                  "abrirConfiguracoes('" + _0x179cd4[0x1] + "()" + "')",
                expandir:
                  "diagramaLimparVars(); diagramaAbrirProg('" +
                  _0x179cd4[0x1] +
                  "', '" +
                  _0x179cd4[0x1] +
                  "', '', 0); docDiagramaDesenhar();",
                group: "Callbacks",
              })),
              !isempty(_0x179cd4[0x2]) &&
                typeof _0x179cd4[0x2].classes != "undefined" &&
                diagramaAbrirProgClasses(
                  _0x179cd4[0x2].classes,
                  _0x179cd4[0x1] + "()",
                  "Callbacks",
                  _0x429715,
                ),
              !isempty(_0x179cd4[0x2]) &&
                typeof _0x179cd4[0x2].progs != "undefined" &&
                diagramaAbrirProgProgs(
                  _0x179cd4[0x2].progs,
                  _0x179cd4[0x1] + "()",
                  "Callbacks",
                  _0x429715,
                ),
              !isempty(_0x179cd4[0x2]) &&
                typeof _0x179cd4[0x2].botoes != "undefined" &&
                diagramaAbrirProgProgs(
                  _0x179cd4[0x2].botoes,
                  _0x179cd4[0x1] + "()",
                  "Botões",
                  _0x429715,
                ));
          })),
        countObj(_0x5d651d.data.classes) > 0x0 &&
          diagramaAbrirProgClasses(
            _0x5d651d.data.classes,
            _0x1117a3,
            "",
            _0x429715,
          ),
        countObj(_0x5d651d.data.progs) > 0x0 &&
          diagramaAbrirProgProgs(
            _0x5d651d.data.progs,
            _0x1117a3,
            "",
            _0x429715,
          ),
        countObj(_0x5d651d.data.botoes) > 0x0 &&
          (jQuery.inArray("Botoes", diagramaProgs) < 0x0 &&
            (diagramaProgs.push("Botoes"),
            diagramaNodeDataArray.push({ key: "Botoes", isGroup: true }),
            diagramaLinkDataArray.push({ from: _0x1117a3, to: "Botoes" })),
          diagramaAbrirProgBotoes(
            _0x5d651d.data.botoes,
            _0x1117a3,
            "Botoes",
            _0x429715,
          )),
        countObj(_0x5d651d.data.js) > 0x0 &&
          (jQuery.inArray("Javascript", diagramaProgs) < 0x0 &&
            (diagramaProgs.push("Javascript"),
            diagramaNodeDataArray.push({ key: "Javascript", isGroup: true }),
            diagramaLinkDataArray.push({ from: _0x1117a3, to: "Javascript" })),
          diagramaAbrirProgJs(
            _0x5d651d.data.js,
            _0x1117a3,
            "Javascript",
            _0x429715,
          )));
    },
  });
}
function diagramaAbrirProgClasses(_0x1a66b7, _0x264777, _0x11822b, _0x40e59a) {
  $.each(_0x1a66b7, function (_0x232804, _0x36d644) {
    var _0x4ba8a1 = "";
    ($.each(g.personalizacaoConf, function (_0x50648a, _0x17d715) {
      ((_0x17d715 = "-" + _0x17d715),
        (strpos(_0x17d715, _0x232804, 0x0) ||
          strpos(_0x17d715, g.empresaAtual + _0x232804, 0x0)) &&
          (_0x4ba8a1 = "(P)"));
    }),
      jQuery.inArray(_0x232804, diagramaProgs) < 0x0 &&
        (diagramaProgs.push(_0x232804),
        diagramaNodeDataArray.push({
          key: _0x232804,
          text: _0x232804.replace(".PHP", ""),
          tipo: "CLASSE",
          personalizado: _0x4ba8a1,
          color: "pink",
          personalizar: "abrirConfiguracoes('" + _0x232804 + "')",
          expandir: "",
          group: _0x11822b,
          category: "classe",
        })),
      _0x264777 &&
        jQuery.inArray(_0x264777 + "<->" + _0x232804, diagramaProgs) < 0x0 &&
        (diagramaProgs.push(_0x264777 + "<->" + _0x232804),
        diagramaLinkDataArray.push({ from: _0x264777, to: _0x232804 })),
      $.each(_0x36d644, function (_0x402bf0, _0x3696e0) {
        var _0x5f4609 = _0x232804 + "->" + _0x3696e0;
        (jQuery.inArray(_0x5f4609, diagramaProgs) < 0x0 &&
          (diagramaProgs.push(_0x5f4609),
          diagramaNodeDataArray.push({
            key: _0x5f4609,
            text: _0x3696e0 + "()",
            tipo: "MÉTODO",
            personalizado: "",
            color: "lightpink",
            personalizar: "abrirConfiguracoes('" + _0x232804 + ".PHP" + "')",
            expandir:
              "diagramaLimparVars(); diagramaAbrirProg('" +
              _0x5f4609 +
              "-Metodo" +
              "', '" +
              _0x5f4609 +
              "()" +
              "', '', 0); docDiagramaDesenhar();",
            group: _0x11822b,
            category: "classe",
          })),
          jQuery.inArray(_0x232804 + "<->" + _0x5f4609, diagramaProgs) < 0x0 &&
            (diagramaProgs.push(_0x232804 + "<->" + _0x5f4609),
            diagramaLinkDataArray.push({ from: _0x232804, to: _0x5f4609 })));
      }));
  });
}
function diagramaAbrirProgProgs(_0x162633, _0x516226, _0x1c9b46, _0x17b750) {
  var _0x3eec9a = _0x516226;
  $.each(_0x162633, function (_0x4b15c7, _0x428364) {
    ((href = _0x428364[0x0].split("/")),
      !isset(href[0x1]) &&
        (href = ["", "vazio", "vazio", "vazio", "vazio", ""]),
      (text = _0x428364[0x1] ? _0x428364[0x1] : href[0x4]),
      _0x428364[0x2] == "botao"
        ? ((keyBotao = _0x428364[0x2] + "---" + _0x428364[0x0]),
          jQuery.inArray(keyBotao, diagramaProgs) < 0x0 &&
            (diagramaProgs.push(keyBotao),
            diagramaNodeDataArray.push({
              key: keyBotao,
              text: text,
              tipo: "BOTÃO",
              personalizado: "",
              color: "lime",
              personalizar: "",
              expandir:
                "diagramaLimparVars(); diagramaAbrirProg('" +
                _0x428364[0x0] +
                "', '" +
                text +
                "', '', 0); docDiagramaDesenhar();",
              group: "Botoes",
              category: "botao",
            })),
          (text = href[0x4]),
          (_0x516226 = keyBotao))
        : (jQuery.inArray(_0x428364[0x0], diagramaProgs) < 0x0 &&
            (diagramaProgs.push(_0x428364[0x0]),
            diagramaNodeDataArray.push({
              key: _0x428364[0x0],
              text: text,
              tipo: href[0x3].toUpperCase(),
              personalizado: temPersonalizacao(_0x428364[0x0]),
              color: hrefCor(_0x428364[0x0]),
              personalizar: "abrirConfiguracoes('" + _0x428364[0x0] + "')",
              expandir:
                "diagramaLimparVars(); diagramaAbrirProg('" +
                _0x428364[0x0] +
                "', '" +
                text +
                "', '', 0); docDiagramaDesenhar();",
              group: _0x1c9b46,
              category: "",
            })),
          _0x516226 &&
            jQuery.inArray(_0x516226 + "<->" + _0x428364[0x0], diagramaProgs) <
              0x0 &&
            (diagramaProgs.push(_0x516226 + "<->" + _0x428364[0x0]),
            diagramaLinkDataArray.push({
              from: _0x516226,
              to: _0x428364[0x0],
            }))),
      (_0x516226 = _0x3eec9a));
  });
}
function diagramaAbrirProgBotoes(_0x28f946, _0x56f21d, _0x4ce69e, _0x4b17b4) {
  $.each(_0x28f946, function (_0x4a7b3f, _0x2ebfb8) {
    ((keyBotao = "BOTAO|||" + _0x56f21d + "|||" + _0x2ebfb8[0x0]),
      jQuery.inArray(keyBotao, diagramaProgs) < 0x0 &&
        (diagramaProgs.push(keyBotao),
        diagramaNodeDataArray.push({
          key: keyBotao,
          text: _0x2ebfb8[0x1],
          tipo: "BOTÃO",
          personalizado: "",
          color: "lime",
          personalizar: "",
          expandir:
            "diagramaLimparVars(); diagramaAbrirProg('" +
            keyBotao +
            "', '" +
            _0x2ebfb8[0x1] +
            "', '', 0); docDiagramaDesenhar();",
          group: _0x4ce69e,
          category: "botao",
        })));
  });
}
function diagramaAbrirProgJs(_0x24a39f, _0x26898a, _0x5b9916, _0x5c0c20) {
  $.each(_0x24a39f, function (_0x5da22e, _0x1cd6ed) {
    ((keyJs = "JS|||" + _0x26898a + "|||" + _0x1cd6ed[0x0]),
      jQuery.inArray(keyJs, diagramaProgs) < 0x0 &&
        (diagramaProgs.push(keyJs),
        diagramaNodeDataArray.push({
          key: _0x1cd6ed[0x1],
          text: _0x1cd6ed[0x0],
          tipo: "JAVASCRIPT",
          personalizado: "",
          color: "gainsboro",
          personalizar: "",
          expandir:
            "diagramaLimparVars(); diagramaAbrirProg('" +
            keyJs +
            "', '" +
            _0x1cd6ed[0x0] +
            "', '', 0); docDiagramaDesenhar();",
          group: _0x5b9916,
          category: "",
        })));
  });
}
function hrefCor(_0x46b1ff) {
  _0x46b1ff = _0x46b1ff.split("/");
  if (_0x46b1ff[0x3] == "content") return "lightblue";
  else {
    if (_0x46b1ff[0x3] == "tab") return "lightyellow";
    else {
      if (_0x46b1ff[0x3] == "grid") return "lightgreen";
      else {
        if (_0x46b1ff[0x3] == "form") return "orange";
        else
          return _0x46b1ff[0x3] == "grid_func-ajax" ||
            _0x46b1ff[0x3] == "form_func-ajax"
            ? "white"
            : "lightgray";
      }
    }
  }
}
function docDiagramaDesenhar() {
  var _0x2dc748 = {
    Fechar: function () {
      $(this).dialog("destroy").remove();
    },
  };
  (dialogConfirm(
    "Documentação online: Diagrama de Fluxo",
    '<div id="myDiagram' +
      diagramaId +
      '" style="background-color: Snow; border: solid 1px gray; width: ' +
      desktopWidth * 0.95 +
      "px; height: " +
      desktopHeight * 0.8 +
      'px"></div>',
    _0x2dc748,
  ),
    setTimeout(function () {
      var _0x130ec7 = go.GraphObject.make;
      ((myDiagram[diagramaId] = _0x130ec7(
        go.Diagram,
        "myDiagram" + diagramaId,
      )),
        (myDiagram[diagramaId].toolManager.mouseWheelBehavior =
          go.ToolManager.WheelZoom),
        (myDiagram[diagramaId].nodeTemplate = _0x130ec7(
          go.Node,
          go.Panel.Auto,
          _0x130ec7(
            go.Shape,
            { figure: "RoundedRectangle" },
            new go.Binding("fill", "color"),
          ),
          _0x130ec7(
            go.Panel,
            go.Panel.Table,
            { defaultAlignment: go.Spot.Left, margin: 0x4 },
            _0x130ec7(go.RowColumnDefinition, { column: 0x1, width: 0x4 }),
            _0x130ec7(
              go.TextBlock,
              {
                row: 0x0,
                column: 0x0,
                columnSpan: 0x1,
                alignment: go.Spot.Center,
              },
              { font: "bold 14pt sans-serif" },
              new go["Binding"]("text", "text"),
            ),
            _0x130ec7(
              go.TextBlock,
              { row: 0x1, column: 0x0 },
              { font: "11pt sans-serif" },
              new go.Binding("text", "key"),
            ),
            _0x130ec7(
              go.TextBlock,
              {
                row: 0x2,
                column: 0x0,
                columnSpan: 0x1,
                alignment: go.Spot.Right,
              },
              { font: "bold 8pt sans-serif" },
              new go.Binding("text", "tipo"),
            ),
            _0x130ec7(
              go.TextBlock,
              {
                row: 0x2,
                column: 0x0,
                columnSpan: 0x1,
                alignment: go.Spot.Left,
              },
              { font: "bold 8pt sans-serif" },
              new go["Binding"]("text", "personalizado"),
            ),
            {
              contextMenu: _0x130ec7(
                go.Adornment,
                go.Panel.Vertical,
                _0x130ec7(
                  "ContextMenuButton",
                  _0x130ec7(go.TextBlock, "Personalizar"),
                  {
                    click: function (_0x15605b, _0x283e2d) {
                      var _0x1435c5 = myDiagram[diagramaId].selection.iterator;
                      _0x1435c5.next();
                      var _0x85c076 = _0x1435c5.value;
                      return (eval(_0x85c076.data.personalizar), false);
                    },
                  },
                ),
              ),
            },
          ),
        )),
        myDiagram[diagramaId].nodeTemplateMap.add(
          "botao",
          _0x130ec7(
            go.Node,
            go.Panel.Auto,
            _0x130ec7(
              go.Shape,
              { figure: "RoundedRectangle" },
              new go["Binding"]("fill", "color"),
            ),
            _0x130ec7(
              go.Panel,
              go.Panel.Table,
              { defaultAlignment: go.Spot.Left, margin: 0x4 },
              _0x130ec7(go.RowColumnDefinition, { column: 0x1, width: 0x4 }),
              _0x130ec7(
                go.TextBlock,
                {
                  row: 0x0,
                  column: 0x0,
                  columnSpan: 0x1,
                  alignment: go.Spot.Center,
                },
                { font: "bold 14pt sans-serif" },
                new go["Binding"]("text", "text"),
              ),
              _0x130ec7(
                go.TextBlock,
                {
                  row: 0x1,
                  column: 0x0,
                  columnSpan: 0x1,
                  alignment: go.Spot.Right,
                },
                { font: "bold 8pt sans-serif" },
                new go["Binding"]("text", "tipo"),
              ),
            ),
          ),
        ),
        myDiagram[diagramaId].nodeTemplateMap.add(
          "classe",
          _0x130ec7(
            go.Node,
            go.Panel.Auto,
            _0x130ec7(
              go.Shape,
              { figure: "RoundedRectangle" },
              new go["Binding"]("fill", "color"),
            ),
            _0x130ec7(
              go.Panel,
              go.Panel.Table,
              { defaultAlignment: go.Spot.Left, margin: 0x4 },
              _0x130ec7(go.RowColumnDefinition, { column: 0x1, width: 0x4 }),
              _0x130ec7(
                go.TextBlock,
                {
                  row: 0x0,
                  column: 0x0,
                  columnSpan: 0x1,
                  alignment: go.Spot.Center,
                },
                { font: "bold 14pt sans-serif" },
                new go["Binding"]("text", "text"),
              ),
              _0x130ec7(
                go.TextBlock,
                {
                  row: 0x2,
                  column: 0x0,
                  columnSpan: 0x1,
                  alignment: go.Spot.Right,
                },
                { font: "bold 8pt sans-serif" },
                new go["Binding"]("text", "tipo"),
              ),
              _0x130ec7(
                go.TextBlock,
                {
                  row: 0x2,
                  column: 0x0,
                  columnSpan: 0x1,
                  alignment: go.Spot.Left,
                },
                { font: "bold 8pt sans-serif" },
                new go["Binding"]("text", "personalizado"),
              ),
              {
                contextMenu: _0x130ec7(
                  go.Adornment,
                  go.Panel.Vertical,
                  _0x130ec7(
                    "ContextMenuButton",
                    _0x130ec7(go.TextBlock, "Personalizar"),
                    {
                      click: function (_0x451fda, _0x400248) {
                        var _0x26ea94 =
                          myDiagram[diagramaId].selection.iterator;
                        _0x26ea94.next();
                        var _0x51539b = _0x26ea94.value;
                        return (eval(_0x51539b.data.personalizar), false);
                      },
                    },
                  ),
                ),
              },
            ),
          ),
        ),
        (myDiagram[diagramaId].groupTemplate = _0x130ec7(
          go.Group,
          go.Panel.Auto,
          {
            layout: _0x130ec7(go.LayeredDigraphLayout, {
              direction: 0x0,
              columnSpacing: 0xa,
            }),
          },
          _0x130ec7(go.Shape, {
            figure: "RoundedRectangle",
            parameter1: 0xa,
            fill: "rgba(128,128,128,0.33)",
          }),
          _0x130ec7(
            go.Panel,
            go.Panel.Vertical,
            _0x130ec7(
              go.TextBlock,
              { font: "Bold 12pt Sans-Serif" },
              new go["Binding"]("text", "key"),
            ),
            _0x130ec7(go.Placeholder, { padding: 0x5, background: "white" }),
          ),
        )),
        (myDiagram[diagramaId].linkTemplate = _0x130ec7(
          go.Link,
          { curve: go.Link.JumpGap, corner: 0x2 },
          _0x130ec7(go.Shape),
          _0x130ec7(go.Shape, { toArrow: "Standard" }),
        )),
        myDiagram[diagramaId].addDiagramListener(
          "ObjectSingleClicked",
          function (_0x8c2567) {
            var _0x4cc822 = _0x8c2567.subject.part;
            _0x4cc822 instanceof go.Node && eval(_0x4cc822.data.expandir);
          },
        ),
        (myDiagram[diagramaId].toolManager.linkingTool.direction =
          go.LinkingTool.ForwardsOnly),
        (myDiagram[diagramaId].initialAutoScale = go.Diagram.Uniform),
        (myDiagram[diagramaId].initialContentAlignment = go.Spot.Center),
        (myDiagram[diagramaId].layout = _0x130ec7(go.LayeredDigraphLayout, {
          isOngoing: false,
          layerSpacing: 0x32,
          direction: 0x5a,
        })),
        (myDiagram[diagramaId].model = new go["GraphLinksModel"](
          diagramaNodeDataArray,
          diagramaLinkDataArray,
        )),
        (myDiagram[diagramaId].undoManager.isEnabled = true),
        jQuery("#myDiagram" + diagramaId).focus());
    }, 0xc8));
}
function isset() {
  var _0x172431 = arguments,
    _0x2686fb = _0x172431.length,
    _0x288a8c = 0x0;
  while (_0x288a8c != _0x2686fb) {
    try {
      if (typeof _0x172431[_0x288a8c] == "undefined") returnfalse;
      else _0x288a8c++;
    } catch (_0x490d92) {
      returnfalse;
    }
  }
  returntrue;
}
function isempty(_0x2c54c6) {
  for (var _0x380bdf in _0x2c54c6) {
    if (_0x2c54c6.hasOwnProperty(_0x380bdf)) returnfalse;
  }
  return _0x2c54c6 == "" || _0x2c54c6 == null || _0x2c54c6 == false
    ? true
    : false;
}
function unique() {
  var _0x171ca6 = "",
    _0x59325a =
      "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";
  for (var _0x163bcc = 0x0; _0x163bcc < 0xd; _0x163bcc++) {
    _0x171ca6 += _0x59325a.substr(Math.floor(Math.random() * 0x3e), 0x1);
  }
  return _0x171ca6;
}
function mt_rand(_0x275a56, _0x317d23) {
  var _0x48bacc = arguments.length;
  if (_0x48bacc === 0x0) ((_0x275a56 = 0x0), (_0x317d23 = 0x7fffffff));
  else {
    if (_0x48bacc === 0x1)
      throw new Error(
        "Warning: mt_rand() expects exactly 2 parameters, 1 given",
      );
  }
  return Math.floor(Math.random() * (_0x317d23 - _0x275a56 + 0x1)) + _0x275a56;
}
function countObj(_0x293e88) {
  var _0x14e30a = 0x0;
  for (var _0x995cde in _0x293e88) {
    _0x14e30a++;
  }
  return _0x14e30a;
}
function Formatar_Valor(
  _0x1060b8,
  _0x3b99a1,
  _0x305721,
  _0x20b1e9,
  _0x56c98f,
  _0x3c9367,
  _0x32caa4,
  _0x58f34a,
) {
  if (_0x56c98f.keyCode == 0xd || _0x56c98f.keyCode == 0x9) {
    if ($(_0x3b99a1).closest("td").hasClass("campoEditavel")) returnfalse;
  }
  var _0x5cfd82 = 0x0,
    _0x9d18b0 = "",
    _0x5817ad = (j = 0x0),
    _0x8bd6d5 = (len2 = 0x0),
    _0x1c919c = "-0123456789",
    _0x392b50 = (aux2 = ""),
    _0x495b87 = window.Event ? _0x56c98f.which : _0x56c98f.keyCode,
    _0x53ff16 = 0x0,
    _0x3c9367,
    _0x2f7dfd = 0x1;
  if (_0x495b87 == 0x8 || _0x495b87 == 0x0 || _0x495b87 == 0xd) returntrue;
  _0x9d18b0 = String.fromCharCode(_0x495b87);
  if (_0x1c919c.indexOf(_0x9d18b0) == -0x1) returnfalse;
  _0x8bd6d5 = _0x3b99a1.value.length;
  for (_0x5817ad = 0x0; _0x5817ad < _0x8bd6d5; _0x5817ad++) {
    _0x3b99a1.value.substr(_0x5817ad, 0x1) != 0x0 &&
      _0x3b99a1.value.substr(_0x5817ad, 0x1) != "," &&
      _0x3b99a1.value.substr(_0x5817ad, 0x1) != "." &&
      _0x2f7dfd++;
  }
  if (_0x8bd6d5 >= _0x32caa4 && _0x2f7dfd == _0x8bd6d5) returnfalse;
  for (_0x5817ad = 0x0; _0x5817ad < _0x8bd6d5; _0x5817ad++)
    if (
      _0x3b99a1.value.charAt(_0x5817ad) != "0" &&
      _0x3b99a1.value.charAt(_0x5817ad) != _0x20b1e9
    )
      break;
  _0x392b50 = "";
  for (; _0x5817ad < _0x8bd6d5; _0x5817ad++)
    if (_0x1c919c.indexOf(_0x3b99a1.value.charAt(_0x5817ad)) != -0x1)
      _0x392b50 += _0x3b99a1.value.charAt(_0x5817ad);
  ((_0x392b50 += _0x9d18b0), (_0x8bd6d5 = _0x392b50.length));
  for (z = 0x1; z < _0x3c9367 - _0x8bd6d5; z++) _0x53ff16 = _0x53ff16 + "0";
  for (c = 0x0; c <= _0x3c9367; c++) {
    if (_0x8bd6d5 != _0x3c9367) {
      if (_0x8bd6d5 == c)
        _0x3b99a1.value = "0" + _0x20b1e9 + _0x53ff16 + _0x392b50;
    } else {
      if (_0x8bd6d5 == _0x3c9367) _0x3b99a1.value = "0" + _0x20b1e9 + _0x392b50;
    }
  }
  if (_0x8bd6d5 > _0x3c9367) {
    aux2 = "";
    for (
      j = 0x0, _0x5817ad = _0x8bd6d5 - (_0x3c9367 + 0x1);
      _0x5817ad >= 0x0;
      _0x5817ad--
    ) {
      (j == 0x3 && ((aux2 += _0x305721), (j = 0x0)),
        (aux2 += _0x392b50.charAt(_0x5817ad)),
        j++);
    }
    ((_0x3b99a1.value = ""), (len2 = aux2.length));
    for (_0x5817ad = len2 - 0x1; _0x5817ad >= 0x0; _0x5817ad--)
      _0x3b99a1.value += aux2.charAt(_0x5817ad);
    _0x3b99a1.value +=
      _0x20b1e9 + _0x392b50.substr(_0x8bd6d5 - _0x3c9367, _0x8bd6d5);
  }
  returnfalse;
}
function Corrige_Valor(_0x4c4d01, _0x372f4a, _0x4000ec) {
  var _0x59d32d = $(_0x372f4a).val();
  ((_0x59d32d = _0x59d32d.replace(".", "")),
    (_0x59d32d = _0x59d32d.replace(",", ".")),
    (_0x59d32d = number_format(_0x59d32d, _0x4000ec, ",", ".")),
    $(_0x372f4a).val(_0x59d32d));
}
function Verifica_Caracter(_0x5b19f4, _0x286506) {
  var _0x442850 = window.Event ? _0x5b19f4.which : _0x5b19f4.keyCode;
  return (_0x442850 > 0x2f && _0x442850 < 0x3a) ||
    _0x442850 < 0x1f ||
    _0x442850 == 0x2c ||
    _0x442850 == 0x7f ||
    _0x442850 == 0x2e ||
    _0x442850 == 0x2d
    ? true
    : false;
}
function number_format(_0x44d496, _0x379383, _0x2a7f93, _0x3c92eb) {
  var _0x55f349 = _0x44d496,
    _0x58bcb2 = _0x379383;
  ((_0x55f349 = !isFinite(+_0x55f349) ? 0x0 : +_0x55f349),
    (_0x58bcb2 = !isFinite(+_0x58bcb2) ? 0x0 : Math.abs(_0x58bcb2)));
  var _0x1c99f9 = typeof _0x3c92eb == "undefined" ? "," : _0x3c92eb,
    _0x3222ff = typeof _0x2a7f93 == "undefined" ? "." : _0x2a7f93,
    _0x39034a =
      _0x58bcb2 > 0x0
        ? _0x55f349.toFixed(_0x58bcb2)
        : Math.round(_0x55f349).toFixed(_0x58bcb2),
    _0x43b518 = Math.abs(_0x55f349).toFixed(_0x58bcb2),
    _0x3b63f7,
    _0x5d5904;
  return (
    _0x43b518 >= 0x3e8
      ? ((_0x3b63f7 = _0x43b518.split(/\D/)),
        (_0x5d5904 = _0x3b63f7[0x0].length % 0x3 || 0x3),
        (_0x3b63f7[0x0] =
          _0x39034a.slice(0x0, _0x5d5904 + (_0x55f349 < 0x0)) +
          _0x3b63f7[0x0]
            .slice(_0x5d5904)
            .replace(/(\d{3})/g, _0x1c99f9 + "$1")),
        (_0x39034a = _0x3b63f7.join(_0x3222ff)))
      : (_0x39034a = _0x39034a.replace(".", _0x3222ff)),
    _0x39034a
  );
}
function Formatar_Tempo(_0xe2ce09) {
  var _0x420c06 = $(_0xe2ce09),
    _0x23c709 = _0x420c06.val(),
    _0x4213d6 = "",
    _0x1af4c9 = 0x0;
  if (_0x23c709.length == 0x1) _0x23c709 = "00:0" + _0x23c709;
  else {
    if (_0x23c709.length < 0x5)
      for (i = _0x23c709.length; i < 0x5; i++) {
        _0x23c709 = "0" + _0x23c709;
      }
  }
  if (_0x23c709.length > 0x5) {
    var _0x20e0d2 = "";
    for (i = _0x23c709.length - 0x5; i < _0x23c709.length; i++) {
      _0x20e0d2 += _0x23c709.charAt(i);
    }
    _0x23c709 = _0x20e0d2;
  }
  for (i = 0x0; i < _0x23c709.length; i++) {
    if (_0x4213d6.length > 0x4) continue;
    var _0x3548bd = _0x23c709.charAt(i);
    _0x3548bd.match(/[0-9]/gi) &&
      (_0x1af4c9++,
      _0x1af4c9 == 0x3 && ((_0x4213d6 += ":"), (_0x1af4c9 = 0x0)),
      (_0x4213d6 += _0x3548bd));
  }
  return (_0x420c06.val(_0x4213d6), false);
}
function Formatar_Data(_0x57e539, _0x16ac14, _0x531ab9, _0x5398a8, _0x285889) {
  var _0x2a3a79 = "0123456789",
    _0x2ed4ba = "",
    _0x325ae8 = window.Event ? _0x531ab9.which : _0x531ab9.keyCode,
    _0xc6ce4a = 0x0,
    _0x33f589 = "";
  if (_0x325ae8 == 0x8 || _0x325ae8 == 0x0 || _0x325ae8 == 0xd) returntrue;
  ((_0x33f589 = _0x16ac14.value), (_0x2ed4ba = String.fromCharCode(_0x325ae8)));
  if (_0x2a3a79.indexOf(_0x2ed4ba) == -0x1) returnfalse;
  _0xc6ce4a = _0x33f589.length;
  if (_0xc6ce4a < 0xa) {
    if (_0x33f589.length == 0x2)
      _0x33f589 = _0x33f589.substring(0x0, 0x2) + _0x5398a8;
    if (_0x33f589.length == 0x5)
      _0x33f589 = _0x33f589.substring(0x0, 0x5) + _0x5398a8;
    _0x16ac14.value = _0x33f589;
  } else _0x16ac14.value = _0x16ac14.value.substring(0x0, _0xc6ce4a - 0x1);
}
function Formatar_Cnpj(_0x2dffce, _0x5f2fe3, _0x1e27b4, _0x1ca510) {
  var _0x33accb = _0x5f2fe3.value,
    _0x540a55 = _0x33accb.length,
    _0x53a279 = "0123456789",
    _0x448f04 = "",
    _0xcfa809 = window.Event ? _0x1e27b4.which : _0x1e27b4.keyCode,
    _0x309076 = _0x33accb;
  if (_0xcfa809 == 0x8 || _0xcfa809 == 0x0 || _0xcfa809 == 0xd) returntrue;
  _0x448f04 = String.fromCharCode(_0xcfa809);
  if (_0x53a279.indexOf(_0x448f04) == -0x1) returnfalse;
  if (_0x540a55 == 0x2) _0x309076 = _0x33accb.substr(0x0, 0x2) + ".";
  if (_0x540a55 == 0x6) _0x309076 = _0x33accb.substr(0x0, 0x6) + ".";
  if (_0x540a55 == 0xa) _0x309076 = _0x33accb.substr(0x0, 0xa) + "/";
  if (_0x540a55 == 0xf) _0x309076 = _0x33accb.substr(0x0, 0xf) + "-";
  _0x5f2fe3.value = _0x309076;
}
function Formatar_Cpf(_0x43a718, _0x1219b0, _0x566cde, _0x51abb1) {
  var _0x7c25c7 = _0x1219b0.value,
    _0x45238e = _0x7c25c7.length,
    _0x482db6 = "0123456789",
    _0x14e44e = "",
    _0x4cd47a = window.Event ? _0x566cde.which : _0x566cde.keyCode,
    _0x26b1d3 = _0x7c25c7;
  if (_0x4cd47a == 0x8 || _0x4cd47a == 0x0 || _0x4cd47a == 0xd) returntrue;
  _0x14e44e = String.fromCharCode(_0x4cd47a);
  if (_0x482db6.indexOf(_0x14e44e) == -0x1) returnfalse;
  if (_0x45238e == 0x3) _0x26b1d3 = _0x7c25c7.substr(0x0, 0x3) + ".";
  if (_0x45238e == 0x7) _0x26b1d3 = _0x7c25c7.substr(0x0, 0x7) + ".";
  if (_0x45238e == 0xb) _0x26b1d3 = _0x7c25c7.substr(0x0, 0xb) + "-";
  _0x1219b0.value = _0x26b1d3;
}
function colunaAcoes(
  _0x5b9665,
  _0x3b1a02,
  _0x322f07,
  _0xfed523,
  _0x55079c,
  _0x3a71cc,
  _0x4ede37,
  _0x33a95f,
  _0x63fb0e,
  _0x3b0fb2,
  _0x39637e,
  _0x463f9e,
  _0x518ff3,
  _0x443b2f,
  _0x1cab36,
  _0x272615,
  _0x25a4f1,
  _0x42a4cd,
  _0x526222,
  _0x255119,
  _0x148c29,
  _0x1f5e31,
  _0x12bb0a,
  _0x293e25,
  _0x24580d,
  _0x2d63c4,
  _0x191414,
  _0x31f440,
  _0x4a70a9,
  _0x1a9ac9,
  _0x5276e0,
  _0x2881d6,
  _0x5f2542,
  _0x49ace0,
  _0x5d9e5c,
  _0x46ba23,
  _0x47eae3,
) {
  var _0x4bec36 = _0x49ace0 ? _0x49ace0 : _0xfed523,
    _0x548d18 = _0x5d9e5c ? _0x5d9e5c : _0xfed523;
  if (!$(_0x5b9665.target).hasClass("ui-widget-header")) returnfalse;
  $(".colunaAcoes").remove();
  var _0x102ebe = $(_0x3b1a02),
    _0x5c04a3 = _0x5b9665.pageX,
    _0x975f4b = _0x102ebe.offset().top + _0x102ebe.outerHeight() + 0x2,
    _0x1ad671 = $("body").width() - 0x226,
    _0x5e9ebb = _0x5c04a3 > _0x1ad671;
  const _0x156e67 = _0x5276e0 ? window.atob(_0x5276e0) : "";
  var _0x587919 = $("<ul></ul>")
    .addClass("dropdown-menu colunaAcoes")
    .attr("role", "menu")
    .css({
      position: "absolute",
      display: "block",
      top: _0x975f4b,
      left: _0x5c04a3,
    });
  _0x63fb0e &&
    ($(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="filtroOrdem(event, \'' +
        _0x463f9e +
        "', '" +
        _0x322f07 +
        "', '" +
        _0xfed523 +
        "', 'ASC'); return false;\"><div class=\"icone ui-icon ui-icon-circle-triangle-s\"></div>&nbsp;Ordenar crescente</a></li>",
    ).appendTo(_0x587919),
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="filtroOrdem(event, \'' +
        _0x463f9e +
        "', '" +
        _0x322f07 +
        "', '" +
        _0xfed523 +
        "', 'DESC'); return false;\"><div class=\"icone ui-icon ui-icon-circle-triangle-n\"></div>&nbsp;Ordenar decrescente</a></li>",
    ).appendTo(_0x587919));
  _0x47eae3.toUpperCase() == "S"
    ? $(
        '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaCongelar', '" +
          "" +
          '\'); return false;"><div class="icone ui-icon ui-icon-unlocked"></div>&nbsp;Descongelar coluna</a></li>',
      ).appendTo(_0x587919)
    : $(
        '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaCongelar', '" +
          "S" +
          '\'); return false;"><div class="icone ui-icon ui-icon-locked"></div>&nbsp;Congelar coluna</a></li>',
      ).appendTo(_0x587919);
  !_0x5f2542 &&
    _0x3b0fb2 &&
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="filtroOcultar(event, \'' +
        _0x463f9e +
        "', '" +
        _0x322f07 +
        "', '" +
        _0xfed523 +
        "', '" +
        _0x272615 +
        '\'); return false;"><div class="icone ui-icon ui-icon-minusthick"></div>&nbsp;Ocultar coluna</a></li>',
    ).appendTo(_0x587919);
  (_0x55079c || _0x12bb0a != "0") &&
    $(
      '<li role="presentation" style="text-align: left;" class="divider"></li>',
    ).appendTo(_0x587919);
  if (_0x55079c)
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="$(\'.colunaAcoes\').remove(); gEditarColuna(this, \'' +
        _0x55079c +
        "', '" +
        _0x3a71cc +
        "', '" +
        _0x4ede37 +
        "', '" +
        _0x33a95f +
        "', '" +
        _0x39637e +
        '\'); event.stopPropagation(); event.preventDefault(); return false;"><div class="icone ui-icon ui-icon-pencil"></div>&nbsp;Editar todos</a></li>',
    ).appendTo(_0x587919);
  else {
  }
  _0x12bb0a != "0" &&
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="$(\'.colunaAcoes\').remove(); divSetarAcaoId(\'' +
        _0x463f9e +
        "', '1', 'colunasOcultas[" +
        _0x12bb0a +
        "]'); divRefresh('" +
        _0x463f9e +
        '\'); event.stopPropagation(); event.preventDefault(); return false;"><div class="icone ui-icon ui-icon-pencil"></div>&nbsp;Editar coluna</a></li>',
    ).appendTo(_0x587919);
  if (_0x518ff3 != "0") {
  }
  (_0x63fb0e || _0x3b0fb2) &&
    $(
      '<li role="presentation" style="text-align: left;" class="divider"></li>',
    ).appendTo(_0x587919);
  if (true || _0x443b2f == "1") {
    var _0x5934fe =
      '\n\t\t\t<li class="dropdown-submenu ' +
      (_0x5e9ebb ? "pull-left" : "") +
      '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#"><div class="icone"></div>&nbsp;Personalizar</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t';
    _0x1cab36 == "0"
      ? (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarAddDelFiltro(event, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x548d18 +
          "', 'add', " +
          (!!_0x49ace0 || _0x46ba23 == "1") +
          '); return false;"><div></div>&nbsp;Adicionar ao filtro</a></li>')
      : (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarAddDelFiltro(event, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x548d18 +
          "', 'del', " +
          (!!_0x49ace0 || _0x46ba23 == "1") +
          '); return false;"><div></div>&nbsp;Remover do filtro</a></li>');
    ((_0x31f440 = _0x31f440 ? JSON.parse(window.atob(_0x31f440)) : {}),
      (_0x5934fe +=
        '\n\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Totais</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t'));
    var _0x3b6316 = function (_0x26e478, _0x8a2529) {
      _0x26e478 = _0x31f440[_0x8a2529]
        ? "Remover " + _0x26e478
        : "Adicionar " + _0x26e478;
      const _0xe92b92 = _0x31f440[_0x8a2529] ? "del" : "add";
      _0x5934fe +=
        '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarAddDelTotais(event, \'' +
        _0x463f9e +
        "', '" +
        _0x272615 +
        "', '" +
        _0x322f07 +
        "', '" +
        _0xfed523 +
        "', '" +
        _0xe92b92 +
        "', '" +
        _0x8a2529 +
        '\'); return false;"><div style="margin-left: -4px; margin-top: 3px;"></div>&nbsp;' +
        _0x26e478 +
        "</a></li>";
    };
    (_0x3b6316("soma", "soma"),
      _0x3b6316("média", "media"),
      _0x3b6316("contador", "count"),
      _0x3b6316("contador único", "distinct"),
      _0x3b6316("menor valor", "min"),
      _0x3b6316("maior valor", "max"),
      (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"));
    if (_0x3b0fb2) {
      ((_0x5934fe +=
        '\n\t\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t\t<a tabindex="-1" href="#">&nbsp;Exibir</a>\n\t\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t\t'),
        (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaExibir'); return false;\"><div></div>&nbsp;Não exibir</a></li>"),
        (_0x5934fe +=
          '\n\t\t\t\t<li class="dropdown-submenu ' +
          (_0x5e9ebb ? "pull-left" : "") +
          '" style="text-align: left;">\n\t\t\t\t\t<a tabindex="-1" href="#">&nbsp;Nível mínimo para exibir</a>\n\t\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t\t'));
      var _0x70bea7 = function (_0x2e0652, _0x2560db) {
        ((bold =
          _0x1f5e31 == _0x2560db || (_0x2560db == 0x1 && !_0x1f5e31)
            ? "font-weight: bold;"
            : ""),
          (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaExibirNivel', '" +
            _0x2560db +
            '\'); return false;" style="' +
            bold +
            '"><div style="margin-left: -4px; margin-top: 3px;"></div>&nbsp;' +
            _0x2e0652 +
            "</a></li>"));
      };
      (_0x70bea7("1", "1"),
        _0x70bea7("2", "2"),
        _0x70bea7("3", "3"),
        _0x70bea7("4", "4"),
        (_0x5934fe += "\n\t\t\t\t\t</ul>\n\t\t\t\t</li>\n\t\t\t"),
        (_0x5934fe += "\n\t\t\t\t\t</ul>\n\t\t\t\t</li>\n\t\t\t"));
    }
    ((_0x5934fe +=
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
      _0x463f9e +
      "', '" +
      _0x272615 +
      "', '" +
      _0x322f07 +
      "', '" +
      _0xfed523 +
      "', 'colunaTitulo'); return false;\"><div></div>&nbsp;Título</a></li>"),
      (_0x5934fe +=
        '\n\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Alinhamento</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t'));
    var _0x5c3bf0 = function (_0x31dac2, _0x3335a4) {
      ((bold = _0x255119 == _0x3335a4 ? "font-weight: bold;" : ""),
        (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaAlinhar', '" +
          _0x3335a4 +
          '\'); return false;" style="' +
          bold +
          '"><div style="margin-left: -4px; margin-top: 3px;"></div>&nbsp;' +
          _0x31dac2 +
          "</a></li>"));
    };
    (_0x5c3bf0("Esquerda", "left"),
      _0x5c3bf0("Centralizado", "center"),
      _0x5c3bf0("Direita", "right"),
      (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"),
      (_0x5934fe +=
        '\n\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Visual</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t'));
    _0x25a4f1
      ? (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaCorDeFundo', 'remover'); return false;\"><div></div>&nbsp;Remover cor de fundo</a></li>")
      : (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'colunaCorDeFundo'); return false;\"><div></div>&nbsp;Alterar cor de fundo</a></li>");
    ((_0x5934fe +=
      "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarCSS/', '&divIdRefresh=' + encodeURIComponent('" +
      _0x463f9e +
      "') + '&gridId=' + encodeURIComponent('" +
      _0x322f07 +
      "') + '&acaoId=' + encodeURIComponent('" +
      _0x4bec36 +
      "') + '&path=' + encodeURIComponent($('#" +
      _0x463f9e +
      "').data('href')), [700,250], false, false, 'center'); return false;\"><div></div>&nbsp;Editar CSS</a></li>"),
      (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"),
      (_0x5934fe +=
        "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarAdicionarCampo/', '&divIdRefresh=' + encodeURIComponent('" +
        _0x463f9e +
        "') + '&gridId=' + encodeURIComponent('" +
        _0x322f07 +
        "') + '&acaoId=' + encodeURIComponent('" +
        _0xfed523 +
        "') + '&titulo=' + encodeURIComponent('" +
        _0x272615 +
        "'), [800,250], false, false, 'center'); return false;\"><div></div>&nbsp;Valor</a></li>"));
    _0x443b2f == "1"
      ? ((_0x5934fe +=
          '\n\t\t\t<li class="dropdown-submenu ' +
          (_0x5e9ebb ? "pull-left" : "") +
          '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Callback</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t'),
        (_0x5934fe +=
          '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
          _0x463f9e +
          "', '" +
          _0x272615 +
          "', '" +
          _0x322f07 +
          "', '" +
          _0x4bec36 +
          "', 'callback', '" +
          _0x293e25 +
          "'); return false;\"><div></div>&nbsp;Nome callback</a></li>"),
        (_0x5934fe +=
          "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarCallbackParameter/', '&divIdRefresh=' + encodeURIComponent('" +
          _0x463f9e +
          "') + '&gridId=' + encodeURIComponent('" +
          _0x322f07 +
          "') + '&acaoId=' + encodeURIComponent('" +
          _0x4bec36 +
          "') + '&callback=' + encodeURIComponent('" +
          _0x293e25 +
          "') + '&callbackParameter=' + encodeURIComponent('" +
          _0x24580d +
          "'), [800,200], false, false, 'center'); return false;\"><div></div>&nbsp;Parâmetros</a></li>"),
        (_0x5934fe +=
          "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarCallback/', '&divIdRefresh=' + encodeURIComponent('" +
          _0x463f9e +
          "') + '&gridId=' + encodeURIComponent('" +
          _0x322f07 +
          "') + '&acaoId=' + encodeURIComponent('" +
          _0x4bec36 +
          "') + '&callback=' + encodeURIComponent('" +
          _0x293e25 +
          "') + '&callbackParameter=' + encodeURIComponent('" +
          _0x24580d +
          "') + '&path=' + encodeURIComponent($('#" +
          _0x463f9e +
          "').data('href')), [900,500], false, false, 'center'); return false;\"><div></div>&nbsp;Editar código</a></li>"),
        (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"))
      : (_0x5934fe +=
          '<li role="presentation" style="text-align: left;" class="disabled"><a role="menuitem" tabindex="-1" href="#"><div></div>&nbsp;Callback</a></li>');
    _0x5934fe +=
      '\n\t\t\t<li class="' +
      (_0x5e9ebb ? "pull-left" : "") +
      " " +
      (_0x191414 == "false" ? "disabled" : "dropdown-submenu") +
      '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Edição</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t';
    if (_0x191414 == "true") {
      _0x42a4cd == "0"
        ? (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaEditar', 'S'); return false;\"><div></div>&nbsp;Habilitar editar célula</a></li>")
        : (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaEditar', ''); return false;\"><div></div>&nbsp;Desabilitar editar célula</a></li>");
      !_0x49ace0 &&
        (_0x526222 == "0"
          ? (_0x5934fe +=
              '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
              _0x463f9e +
              "', '" +
              _0x272615 +
              "', '" +
              _0x322f07 +
              "', '" +
              _0x4bec36 +
              "', 'colunaEditarTodos', 'S'); return false;\"><div></div>&nbsp;Habilitar editar todos</a></li>")
          : (_0x5934fe +=
              '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
              _0x463f9e +
              "', '" +
              _0x272615 +
              "', '" +
              _0x322f07 +
              "', '" +
              _0x4bec36 +
              "', 'colunaEditarTodos', ''); return false;\"><div></div>&nbsp;Desabilitar editar todos</a></li>"));
      _0x12bb0a == "0"
        ? (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaEditarOculto', 'S'); return false;\"><div></div>&nbsp;Habilitar editar coluna</a></li>")
        : (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaEditarOculto', ''); return false;\"><div></div>&nbsp;Desabilitar editar coluna</a></li>");
      _0x5934fe +=
        '\n\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Nível mínimo para editar</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t\t';
      var _0x43da3c = function (_0x56f5aa, _0x49feb1) {
        ((bold =
          _0x148c29 == _0x49feb1 || (_0x49feb1 == 0x1 && !_0x148c29)
            ? "font-weight: bold;"
            : ""),
          (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0x4bec36 +
            "', 'colunaEditarNivel', '" +
            _0x49feb1 +
            '\'); return false;" style="' +
            bold +
            '"><div style="margin-left: -4px; margin-top: 3px;"></div>&nbsp;' +
            _0x56f5aa +
            "</a></li>"));
      };
      (_0x43da3c("1", "1"),
        _0x43da3c("2", "2"),
        _0x43da3c("3", "3"),
        _0x43da3c("4", "4"),
        (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t\t"),
        (_0x5934fe +=
          "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarCallback/', '&divIdRefresh=' + encodeURIComponent('" +
          _0x463f9e +
          "') + '&gridId=' + encodeURIComponent('" +
          _0x322f07 +
          "') + '&acaoId=' + encodeURIComponent('" +
          _0x4bec36 +
          "') + '&callback=' + encodeURIComponent('" +
          _0x2881d6 +
          "') + '&callbackParameter=' + encodeURIComponent('" +
          _0x24580d +
          "') + '&path=' + encodeURIComponent($('#" +
          _0x463f9e +
          "').data('href')) + '&tipo=colunaEditarCallback', [900,500], false, false, 'center'); return false;\"><div></div>&nbsp;Callback</a></li>"));
    }
    ((_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"),
      (_0x5934fe +=
        '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
        _0x463f9e +
        "', '" +
        _0x272615 +
        "', '" +
        _0x322f07 +
        "', '" +
        _0x4bec36 +
        "', 'colunaCasasDecimais', '" +
        _0x2d63c4 +
        "'); return false;\"><div></div>&nbsp;Casas decimais</a></li>"),
      (_0x5934fe +=
        '\n\t\t\t<li class="dropdown-submenu ' +
        (_0x5e9ebb ? "pull-left" : "") +
        '" style="text-align: left;">\n\t\t\t\t<a tabindex="-1" href="#">&nbsp;Agrupamento</a>\n\t\t\t\t<ul class="dropdown-menu dropup">\n\t\t'),
      _0x4a70a9 == 0x1
        ? (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0xfed523 +
            "', 'colunaDimensao', ''); return false;\"><div></div>&nbsp;Remover dimensão</a></li>")
        : _0x156e67.match(/group_concat/i)
          ? (_0x5934fe +=
              '<li role="presentation" style="text-align: left;" class="disabled"><a role="menuitem" tabindex="-1" href="#"><div></div>&nbsp;Adicionar dimensão</a></li>')
          : (_0x5934fe +=
              '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
              _0x463f9e +
              "', '" +
              _0x272615 +
              "', '" +
              _0x322f07 +
              "', '" +
              _0xfed523 +
              "', 'colunaDimensao', 'S'); return false;\"><div></div>&nbsp;Adicionar dimensão</a></li>"),
      _0x1a9ac9 == 0x1
        ? (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0xfed523 +
            "', 'colunaMetrica', ''); return false;\"><div></div>&nbsp;Remover métrica</a></li>")
        : (_0x5934fe +=
            '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="personalizarGrid(event, this, \'' +
            _0x463f9e +
            "', '" +
            _0x272615 +
            "', '" +
            _0x322f07 +
            "', '" +
            _0xfed523 +
            "', 'colunaMetrica', 'S'); return false;\"><div></div>&nbsp;Adicionar métrica</a></li>"),
      (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"),
      (_0x5934fe += "\n\t\t\t\t</ul>\n\t\t\t</li>\n\t\t"),
      $(_0x5934fe).appendTo(_0x587919),
      $(
        '<li role="presentation" style="text-align: left;" class="divider"></li>',
      ).appendTo(_0x587919));
  }
  ($(
    '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="event.stopPropagation(); wikiAjuda(\'' +
      _0x463f9e +
      "', 'GRID', 'COLUNA', '" +
      _0xfed523 +
      "', '" +
      _0x272615 +
      '\'); return false;"><div class="icone ui-icon ui-icon-help"></div>&nbsp;Ajuda</a></li>',
  ).appendTo(_0x587919),
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="event.stopPropagation(); $(\'.colunaAcoes\').remove(); return false;"><div class="icone ui-icon ui-icon-closethick"></div>&nbsp;Fechar</a></li>',
    ).appendTo(_0x587919),
    _0x587919.appendTo("body"));
  var _0x5ed2f6 = $("body").height() - 0x199;
  _0x975f4b > _0x5ed2f6 && _0x587919.addClass("dropup");
  _0x587919.outerHeight() + _0x587919.offset().top > $("body").height() - 0xa &&
    ((_0x975f4b -= _0x587919.outerHeight() + _0x102ebe.outerHeight() + 0x5),
    _0x587919.css({ top: _0x975f4b }),
    _0x587919.addClass("dropup"));
  if (_0x587919.hasClass("dropup") && _0x975f4b - 0x14b < desktopUp) {
    var _0x4c7156 = _0x587919.find(".dropdown-menu .dropdown-menu:first");
    _0x4c7156.css({ top: "0", bottom: "unset" });
  }
  (_0x587919.outerWidth() + _0x587919.offset().left > $("body").width() - 0xa &&
    ((_0x5c04a3 -= _0x587919.outerWidth()), _0x587919.css({ left: _0x5c04a3 })),
    _0x587919.data("th", _0x3b1a02),
    _0x5b9665.stopPropagation(),
    _0x5b9665.preventDefault());
}
function colunaAcoesCountLinha(
  _0x1eae4d,
  _0x493a28,
  _0x55ea61,
  _0x3d38f0,
  _0x3f475d,
) {
  $(".colunaAcoes").remove();
  var _0x2f104e = $(_0x493a28),
    _0x10b93a = _0x1eae4d.pageX,
    _0x37cc17 = _0x2f104e.offset().top + _0x2f104e.outerHeight() + 0x2,
    _0x261c1e = $("<ul></ul>")
      .addClass("dropdown-menu colunaAcoes")
      .attr("role", "menu")
      .css({
        display: "block",
        position: "absolute",
        top: _0x37cc17,
        left: _0x10b93a,
      });
  ((menuPersonalizar = ""),
    (menuPersonalizar +=
      "<li role=\"presentation\" style=\"text-align: left;\"><a role=\"menuitem\" tabindex=\"-1\" href=\"#\" onclick=\"abrirJanela(event, divIdRootAberto(), '', menuId('personalizacaoLowCode'), 'Personalização Grid', '.: Personalização :.', '/sistema/personalizacaoLowCode/content/editarGridLinha/', '&divIdRefresh=' + encodeURIComponent('" +
      _0x3f475d +
      "') + '&gridId=' + encodeURIComponent('" +
      _0x55ea61 +
      "') + '&acaoId=' + encodeURIComponent('" +
      _0x3d38f0 +
      "') + '&path=' + encodeURIComponent($('#" +
      _0x3f475d +
      "').data('href')), [900,500], false, false, 'center'); return false;\"><div></div>&nbsp;Editar Função Linha</a></li>"),
    $(menuPersonalizar).appendTo(_0x261c1e),
    $(
      '<li role="presentation" style="text-align: left;" class="divider"></li>',
    ).appendTo(_0x261c1e),
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="event.stopPropagation(); $(\'.colunaAcoes\').remove(); return false;"><div class="icone ui-icon ui-icon-closethick"></div>&nbsp;Fechar</a></li>',
    ).appendTo(_0x261c1e),
    _0x261c1e.appendTo("body"),
    _0x1eae4d.stopPropagation(),
    _0x1eae4d.preventDefault());
}
function filtroOcultar(_0x1e3dd2, _0x82864e, _0x6a97dc, _0x1bba64, _0x90d5) {
  return (
    _0x1e3dd2.stopPropagation(),
    $("#" + _0x82864e).showLoading(),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/ocultar/?ajax=true&campo=" +
        encodeURIComponent(_0x1bba64) +
        "&grid=" +
        encodeURIComponent(_0x6a97dc) +
        "&titulo=" +
        encodeURIComponent(_0x90d5) +
        "&callback=?",
      function (_0x5ea026) {
        $("#" + _0x82864e).hideLoading();
        if (_0x5ea026.code) divRefresh(_0x82864e, true);
        else {
          var _0x1bb0bd = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x5ea026.data, _0x1bb0bd);
        }
      },
    ),
    false
  );
}
function filtroMostrar(_0x55199a, _0x432488, _0x299e0a) {
  return (
    $("#" + _0x55199a).showLoading(),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/mostrar/?ajax=true&campo=" +
        encodeURIComponent(_0x299e0a) +
        "&grid=" +
        encodeURIComponent(_0x432488) +
        "&callback=?",
      function (_0xe615be) {
        $("#" + _0x55199a).hideLoading();
        if (_0xe615be.code) divRefresh(_0x55199a, true);
        else {
          var _0x43a47f = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0xe615be.data, _0x43a47f);
        }
      },
    ),
    false
  );
}
function janelaMaisOpcoes(_0x30978c) {
  ($(".colunaAcoes").remove(),
    $(".list-options-form").remove(),
    $(".janelaMaisOpcoes").remove(),
    $("body")
      .unbind("click.janelaMaisOpcoes")
      .bind("click.janelaMaisOpcoes", function (_0x3ee433) {
        !$(_0x3ee433.target).hasClass("gridTheadTh") &&
          !$(_0x3ee433.target).hasClass("fa-bars") &&
          $(".janelaMaisOpcoes").remove();
      }));
  var _0x576021 = $("<ul></ul>")
      .addClass("dropdown-menu pull-left janelaMaisOpcoes")
      .attr("role", "menu")
      .css({
        display: "block",
        top: "11px",
        left: "-134px",
        position: "absolute",
      }),
    _0x5dd717 = 0x1,
    _0x4e6fa6 = 0x1,
    _0x454d93 = false,
    _0x26db1a = _0x30978c.data("exibirAuditoria") ? "" : "disabled";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'auditoria\');"><div class="icone ui-icon ui-icon-note" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Auditoria</a></li>',
  ).appendTo(_0x576021);
  if (_0x30978c.data("exibirTrocarEmp"))
    var _0x26db1a = "",
      _0xfd5d86 = "janelaMaisOpcoesExecutar(this, 'trocaremp');";
  else
    var _0x26db1a = "disabled",
      _0xfd5d86 = "";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="' +
      _0xfd5d86 +
      '"><div class="icone ui-icon ui-icon-transfer-e-w" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Trocar de Empresa</a></li>',
  ).appendTo(_0x576021);
  var _0x26db1a = _0x30978c.data("exibirDuplicar") ? "" : "disabled";
  ($(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'duplicar\');"><div class="icone ui-icon ui-icon-extlink" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Duplicar</a></li>',
  ).appendTo(_0x576021),
    $(
      '<li class="' +
        _0x26db1a +
        '" role="presentation" style="text-align: left;" class="divider"></li>',
    ).appendTo(_0x576021));
  var _0x26db1a = _0x30978c.data("exibirBusca") ? "" : "disabled";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'busca\');"><div class="icone ui-icon ui-icon-search" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Pesquisar</a></li>',
  ).appendTo(_0x576021);
  var _0x26db1a = _0x30978c.data("exibirPrimeiro") ? "" : "disabled";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'primeiro\');"><div class="icone ui-icon ui-icon-seek-first" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Ir para o Primeiro Registro</a></li>',
  ).appendTo(_0x576021);
  var _0x26db1a = _0x30978c.data("exibirAnterior") ? "" : "disabled";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'anterior\');"><div class="icone ui-icon ui-icon-triangle-1-w" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Registro Anterior</a></li>',
  ).appendTo(_0x576021);
  var _0x26db1a = _0x30978c.data("exibirProximo") ? "" : "disabled";
  $(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'proximo\');"><div class="icone ui-icon ui-icon-triangle-1-e" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Próximo Registro</a></li>',
  ).appendTo(_0x576021);
  var _0x26db1a = _0x30978c.data("exibirUltimo") ? "" : "disabled";
  ($(
    '<li class="' +
      _0x26db1a +
      '" role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'ultimo\');"><div class="icone ui-icon ui-icon-seek-end" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Ir para o Último Registro</a></li>',
  ).appendTo(_0x576021),
    $(
      '<li role="presentation" style="text-align: left;" class="divider"></li>',
    ).appendTo(_0x576021),
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="wikiAjuda(\'' +
        _0x30978c
          .closest(".ui-dialog")
          .find(".ui-dialog-content:first")
          .attr("id") +
        '\'); return false;"><div class="icone ui-icon ui-icon-help" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Ajuda</a></li>',
    ).appendTo(_0x576021),
    $(
      '<li role="presentation" style="text-align: left;"><a role="menuitem" tabindex="-1" href="#" onclick="janelaMaisOpcoesExecutar(this, \'fechar\');"><div class="icone ui-icon ui-icon-closethick" style="margin-left: -4px; margin-top: -1px"></div>&nbsp;Fechar</a></li>',
    ).appendTo(_0x576021),
    _0x576021.appendTo(_0x30978c.parent()));
}
function janelaMaisOpcoesExecutar(_0x54e347, _0x4441de) {
  var _0x39e897 = $(_0x54e347).parent().closest("a").find(".fa-bars"),
    _0x605c3d = _0x39e897.closest(".ui-dialog").find(".content:eq(0)"),
    _0xec5a9c = _0x39e897.data("table"),
    _0x51b9c3 = _0x39e897.data("id");
  if (_0x4441de == "fechar")
    setTimeout(function () {
      ($(".janelaMaisOpcoes").remove(),
        $(".fa-bars").parent().removeClass("ui-state-hover"));
    }, 0x64);
  else {
    if (_0x4441de == "auditoria")
      (setTimeout(function () {
        ($(".janelaMaisOpcoes").remove(),
          $(".fa-bars").parent().removeClass("ui-state-hover"));
      }, 0x64),
        gridAudit(_0x605c3d, _0xec5a9c, _0x51b9c3));
    else {
      if (_0x4441de == "trocaremp")
        (setTimeout(function () {
          ($(".janelaMaisOpcoes").remove(),
            $(".fa-bars").parent().removeClass("ui-state-hover"));
        }, 0x64),
          trocarEmp(_0x605c3d, _0xec5a9c, _0x51b9c3));
      else {
        if (_0x4441de == "duplicar") {
          setTimeout(function () {
            ($(".janelaMaisOpcoes").remove(),
              $(".fa-bars").parent().removeClass("ui-state-hover"));
          }, 0x64);
          var _0x2b167f = _0x605c3d
              .find(".ui-tabs-panel:visible:first")
              .data("divId"),
            _0x4afbdb = $("#" + _0x2b167f + " form:first")
              .parent()
              .data("href"),
            _0x3ba571 = $("#" + _0x2b167f + " form:first")
              .parent()
              .data("divIdRoot");
          typeof _0x4afbdb == "undefined" &&
            ((_0x4afbdb = $("#" + _0x2b167f).data("href")),
            (_0x3ba571 = $("#" + _0x2b167f).data("divIdRoot")));
          if (typeof _0x4afbdb != "undefined") {
            var _0x182250 = _0x4afbdb.split("/");
            if (_0x182250[0x3] == "form") {
              ((classe = _0x182250[0x2].toUpperCase()),
                (method = _0x182250[0x4]));
              var _0x3b53a9 = {
                Sim: function () {
                  ($.getJSON(
                    "/sistema/funcoes/controleFormulario/duplicarRegistro/?ajax=true&classe=" +
                      encodeURIComponent(classe) +
                      "&case=" +
                      encodeURIComponent(method) +
                      "&divIdRoot=" +
                      encodeURIComponent(_0x3ba571) +
                      "&divId=" +
                      encodeURIComponent(_0x2b167f) +
                      "&tabela=" +
                      encodeURIComponent(_0xec5a9c) +
                      "&id=" +
                      encodeURIComponent(_0x51b9c3) +
                      "&callback=?",
                    function (_0x3ed1dd) {
                      eval(_0x3ed1dd.data);
                    },
                  ),
                    $(this).dialog("destroy").remove());
                },
                Não: function () {
                  $(this).dialog("destroy").remove();
                },
              };
              dialogConfirm(
                "Atenção",
                "Você deseja duplicar este registro? ",
                _0x3b53a9,
              );
            }
          }
        } else {
          if (_0x4441de == "busca") {
            setTimeout(function () {
              ($(".janelaMaisOpcoes").remove(),
                $(".fa-bars").parent().removeClass("ui-state-hover"));
            }, 0x64);
            var _0xec3f44 = _0x605c3d.data("divId"),
              _0x2b167f = _0x605c3d
                .find(".ui-tabs-panel:visible:first")
                .data("divId"),
              _0x317775 = _0x39e897
                .parent()
                .find(".ui-dialog-content:first")
                .attr("id");
            _0x317775 = $("#" + _0x317775).data("janelaHref");
            var _0x3ba571 = $("#" + _0x2b167f + " form:first")
                .parent()
                .data("divIdRoot"),
              _0x212af1 = $("#" + _0x2b167f + " form:first")
                .parent()
                .data("divId"),
              _0x4afbdb = $("#" + _0x2b167f + " form:first")
                .parent()
                .data("href");
            typeof _0x4afbdb == "undefined" &&
              ((_0x4afbdb = $("#" + _0x2b167f).data("href")),
              (_0x3ba571 = $("#" + _0x2b167f).data("divIdRoot")),
              (_0x212af1 = $("#" + _0x2b167f).data("divId")));
            if (typeof _0x4afbdb != "undefined") {
              var _0x182250 = _0x4afbdb.split("/");
              if (_0x182250[0x3] == "form") {
                var _0x189d45 = $("#" + _0x212af1).data("sqlPesquisa"),
                  _0xd6c975 = $("#" + _0x212af1).data("labelPesquisa"),
                  _0x145fcf = $("#" + _0x212af1).data("sqlExtra");
                _0x189d45 &&
                  eval(
                    "abrirJanela(![], _0x3ba571, _0x2b167f, unique(), '', _0x5104e8(1951), _0x5104e8(1705), '&sql=' + encodeURIComponent(_0x189d45) + _0x5104e8(506) + encodeURIComponent(_0x145fcf) + '&labels=' + encodeURIComponent(_0xd6c975) + _0x5104e8(320) + encodeURIComponent(_0xec3f44), [\n    375,\n    75\n]);",
                  );
              }
            }
          } else {
            if (_0x4441de == "primeiro")
              primeiroId(_0x605c3d, _0x51b9c3, _0xec5a9c, _0x39e897);
            else {
              if (_0x4441de == "anterior")
                voltarId(_0x605c3d, _0x51b9c3, _0xec5a9c, _0x39e897);
              else {
                if (_0x4441de == "proximo")
                  avancarId(_0x605c3d, _0x51b9c3, _0xec5a9c, _0x39e897);
                else
                  _0x4441de == "ultimo" &&
                    ultimoId(_0x605c3d, _0x51b9c3, _0xec5a9c, _0x39e897);
              }
            }
          }
        }
      }
    }
  }
}
function filtroOrdem(_0x80d499, _0x590bc4, _0xa323a3, _0x3918e0, _0x53ac27) {
  (_0x80d499.stopPropagation(), $("#" + _0x590bc4).showLoading());
  var _0x15572e = "";
  return (
    _0x80d499.ctrlKey && (_0x15572e = "true"),
    $.getJSON(
      "/sistema/funcoes/gridFiltro/ordenar/?ajax=true&ordem=" +
        encodeURIComponent(_0x3918e0) +
        "&grid=" +
        encodeURIComponent(_0xa323a3) +
        "&ascdesc=" +
        encodeURIComponent(_0x53ac27) +
        "&ctrlKey=" +
        _0x15572e +
        "&callback=?",
      function (_0xa1b0aa) {
        $("#" + _0x590bc4).hideLoading();
        if (_0xa1b0aa.code) divRefresh(_0x590bc4, true);
        else {
          var _0x4e7b3f = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0xa1b0aa.data, _0x4e7b3f);
        }
      },
    ),
    false
  );
}
function personalizarAddDelFiltro(
  _0x30ecee,
  _0x510971,
  _0xfa1039,
  _0xf35e94,
  _0x3b3f8a,
  _0x2dc152,
  _0x2f59b8,
) {
  return (
    _0x30ecee.stopPropagation(),
    $("#" + _0x510971).showLoading(),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/adicionarRemoverFiltro/?ajax=true&colunaValor=" +
        encodeURIComponent(_0x3b3f8a) +
        "&titulo=" +
        encodeURIComponent(_0xfa1039) +
        "&grid=" +
        encodeURIComponent(_0xf35e94) +
        "&acao=" +
        encodeURIComponent(_0x2dc152) +
        "&having=" +
        (_0x2f59b8 ? "S" : "") +
        "&callback=?",
      function (_0x135fbe) {
        $("#" + _0x510971).hideLoading();
        if (_0x135fbe.code) divRefresh(_0x510971, true);
        else {
          var _0x327676 = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x135fbe.data, _0x327676);
        }
      },
    ),
    false
  );
}
function personalizarAddDelTotais(
  _0x588a94,
  _0x772e82,
  _0x133c46,
  _0x48f43c,
  _0x5150d9,
  _0xc20697,
  _0xcf382e,
) {
  return (
    _0x588a94.stopPropagation(),
    $("#" + _0x772e82).showLoading(),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/adicionarRemoverTotais/?ajax=true&colunaValor=" +
        encodeURIComponent(_0x5150d9) +
        "&titulo=" +
        encodeURIComponent(_0x133c46) +
        "&grid=" +
        encodeURIComponent(_0x48f43c) +
        "&acao=" +
        encodeURIComponent(_0xc20697) +
        "&totalizador=" +
        encodeURIComponent(_0xcf382e) +
        "&callback=?",
      function (_0x30dbee) {
        $("#" + _0x772e82).hideLoading();
        if (_0x30dbee.code) divRefresh(_0x772e82, true);
        else {
          var _0x17af8c = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x30dbee.data, _0x17af8c);
        }
      },
    ),
    false
  );
}
function personalizarGrid(
  _0x5307a8,
  _0x43166a,
  _0x2f6d74,
  _0x5286e0,
  _0x178fac,
  _0x24e8af,
  _0x346ec9,
  _0x5c4f81,
) {
  _0x5307a8.stopPropagation();
  (_0x346ec9 != "colunaCorDeFundo" || _0x5c4f81 == "remover") &&
    $("#" + _0x2f6d74).showLoading();
  let _0x109766 = "";
  if (_0x346ec9 == "colunaExibir") _0x109766 = "";
  else {
    if (_0x346ec9 == "colunaTitulo") {
      _0x109766 = prompt("Alterar título da coluna", _0x5286e0);
      if (!_0x109766)
        return (
          $(".colunaAcoes").remove(),
          $("#" + _0x2f6d74).hideLoading(),
          false
        );
    } else {
      if (_0x346ec9 == "colunaCorDeFundo" && _0x5c4f81 != "remover") {
        var _0x1cadc0 = $(".colunaAcoes:first"),
          _0x467b2b = $(_0x1cadc0.data("th")),
          _0x4ee70e = false,
          _0x4c0fa3 = +0x19;
        _0x1cadc0.hasClass("dropup") &&
          ((_0x4ee70e = true), (_0x4c0fa3 = -0x5));
        var _0x361130 = _0x467b2b.offset().left + _0x467b2b.width() + _0x4c0fa3,
          _0x26b5c6 = false;
        0xfa + _0x361130 > $("body").width() - 0xa &&
          ((_0x26b5c6 = true), (_0x361130 = _0x467b2b.offset().left));
        $('<span id="picker"></span>')
          .css({
            display: "block",
            position: "absolute",
            float: "left",
            top: _0x467b2b.offset().top + _0x467b2b.height() - 0xe,
            left: _0x361130,
            "z-index": 0x2710,
          })
          .appendTo("body");
        var _0x2790b8 = new Picker({
          parent: document.querySelector("#picker"),
          color: "gray",
          popup: _0x4ee70e ? "top" : _0x26b5c6 ? "left" : "right",
          onDone: function (_0x2a2de5) {
            ((_0x109766 =
              _0x2a2de5.hex + "," + shadeColor(_0x2a2de5.hex, -0x14)),
              $("#picker").remove(),
              $.getJSON(
                "/sistema/personalizacaoGrid/form_func-ajax/personalizarGrid/?ajax=true&colunaValor=" +
                  encodeURIComponent(_0x24e8af) +
                  "&colunaTitulo=" +
                  encodeURIComponent(_0x5286e0) +
                  "&grid=" +
                  encodeURIComponent(_0x178fac) +
                  "&opcao=" +
                  encodeURIComponent(_0x346ec9) +
                  "&opcaoValor=" +
                  encodeURIComponent(_0x109766) +
                  "&callback=?",
                function (_0x513cd0) {
                  $("#" + _0x2f6d74).hideLoading();
                  if (_0x513cd0.code) divRefresh(_0x2f6d74, true);
                  else {
                    var _0x1d0366 = {
                      Fechar: function () {
                        $(this).dialog("destroy").remove();
                      },
                    };
                    dialogConfirm("Erro", _0x513cd0.data, _0x1d0366);
                  }
                },
              ));
          },
          onClose: function () {
            (_0x2790b8.destroy(), $("#picker").remove());
          },
        });
        return (_0x2790b8.show(), $(".colunaAcoes").remove(), false);
      } else {
        if (_0x346ec9 == "colunaCorDeFundo" && _0x5c4f81 == "remover")
          _0x109766 = "";
        else {
          if (_0x346ec9 == "callback") {
            _0x109766 = prompt("Alterar nome do callback", _0x5c4f81);
            if (_0x109766 == null)
              return (
                $(".colunaAcoes").remove(),
                $("#" + _0x2f6d74).hideLoading(),
                false
              );
          } else {
            if (_0x346ec9 == "colunaCasasDecimais") {
              _0x109766 = prompt("Número de casas decimais", _0x5c4f81);
              if (_0x109766 == null)
                return (
                  $(".colunaAcoes").remove(),
                  $("#" + _0x2f6d74).hideLoading(),
                  false
                );
            } else _0x109766 = _0x5c4f81;
          }
        }
      }
    }
  }
  return (
    (extra = _0x346ec9 == "callback" ? "&alterandoPara=" : ""),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/personalizarGrid/?ajax=true" +
        "&colunaValor=" +
        encodeURIComponent(_0x24e8af) +
        "&colunaTitulo=" +
        encodeURIComponent(_0x5286e0) +
        "&grid=" +
        encodeURIComponent(_0x178fac) +
        "&opcao=" +
        encodeURIComponent(_0x346ec9) +
        "&opcaoValor=" +
        encodeURIComponent(_0x109766) +
        extra +
        "&callback=?",
      function (_0x168169) {
        $("#" + _0x2f6d74).hideLoading();
        if (_0x168169.code) divRefresh(_0x2f6d74, true);
        else {
          var _0x114a44 = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x168169.data, _0x114a44);
        }
      },
    ),
    false
  );
}
function shadeColor(_0x23866a, _0x373633) {
  var _0x53f824 = parseInt(_0x23866a.substring(0x1, 0x3), 0x10),
    _0x2dee1c = parseInt(_0x23866a.substring(0x3, 0x5), 0x10),
    _0x4aecf9 = parseInt(_0x23866a.substring(0x5, 0x7), 0x10);
  ((_0x53f824 = parseInt((_0x53f824 * (0x64 + _0x373633)) / 0x64)),
    (_0x2dee1c = parseInt((_0x2dee1c * (0x64 + _0x373633)) / 0x64)),
    (_0x4aecf9 = parseInt((_0x4aecf9 * (0x64 + _0x373633)) / 0x64)),
    (_0x53f824 = _0x53f824 < 0xff ? _0x53f824 : 0xff),
    (_0x2dee1c = _0x2dee1c < 0xff ? _0x2dee1c : 0xff),
    (_0x4aecf9 = _0x4aecf9 < 0xff ? _0x4aecf9 : 0xff));
  var _0xf2cd1d =
      _0x53f824.toString(0x10).length == 0x1
        ? "0" + _0x53f824.toString(0x10)
        : _0x53f824.toString(0x10),
    _0x9121b8 =
      _0x2dee1c.toString(0x10).length == 0x1
        ? "0" + _0x2dee1c.toString(0x10)
        : _0x2dee1c.toString(0x10),
    _0xd73601 =
      _0x4aecf9.toString(0x10).length == 0x1
        ? "0" + _0x4aecf9.toString(0x10)
        : _0x4aecf9.toString(0x10);
  return "#" + _0xf2cd1d + _0x9121b8 + _0xd73601;
}
function acoesGrid(
  _0x56a417,
  _0x1cb4a9,
  _0x1bcda1,
  _0x3dcd98,
  _0x5e2ca6,
  _0x204b6b,
  _0x496fa9,
) {
  var _0x1cb4a9 = typeof _0x1cb4a9 == "undefined" ? false : _0x1cb4a9,
    _0x1bcda1 = typeof _0x1bcda1 == "undefined" ? false : _0x1bcda1,
    _0x3dcd98 = typeof _0x3dcd98 == "undefined" ? false : _0x3dcd98,
    _0x5e2ca6 = typeof _0x5e2ca6 == "undefined" ? false : _0x5e2ca6,
    _0x204b6b = typeof _0x204b6b == "undefined" ? true : _0x204b6b,
    _0x496fa9 = typeof _0x496fa9 == "undefined" ? false : _0x496fa9,
    _0x429596 = $(_0x56a417),
    _0x2bc617 = getDivIdTab(_0x429596);
  if (_0x1cb4a9) {
    var _0x205881 = $("#" + _0x2bc617);
    (_0x205881.showLoading(),
      $.ajax({
        url: _0x205881.data("href"),
        dataType: "html",
        data:
          "ajax=true&clearFILTER=true&divIdRoot=" +
          _0x205881.data("divIdRoot") +
          "&divIdParent=" +
          _0x205881.data("divIdParent") +
          "&divIdParentChain=" +
          _0x205881.data("divIdParentChain") +
          "&divId=" +
          _0x205881.data("divId") +
          "&divIdChain=" +
          _0x205881.data("divIdChain") +
          _0x205881.data("vars"),
        success: function (_0x5e66fa) {
          return (_0x205881.hideLoading(), divRefresh(_0x2bc617), false);
        },
      }));
  }
  if (_0x3dcd98 || _0x5e2ca6 || _0x496fa9 || _0x1bcda1) {
    var _0x205881 = $("#" + _0x2bc617),
      _0x2f1529 = _0x205881.data("href").match(/\/(.*)/);
    _0x2f1529 = _0x2f1529[0x1];
    if (_0x2f1529.match(/etc011GridPivot/))
      return (
        acoesGridImpHtml(_0x2bc617, "", _0x3dcd98, _0x5e2ca6, false, _0x496fa9),
        false
      );
    else
      (_0x205881.showLoading(),
        $.ajax({
          url: _0x205881.data("href"),
          dataType: "html",
          data:
            "ajax=true&gridFiltrado=true&pegarHTMLColunas=true" +
            _0x205881.data("vars"),
          type: "post",
          success: function (_0x42215c) {
            _0x205881.hideLoading();
            var _0x17c25e = _0x42215c.match(
                /<pegarHTMLColunas>([\s\S]*)<\/pegarHTMLColunas>/m,
              ),
              _0xcd8cae = _0x17c25e[0x1];
            return (
              acoesGridImpHtml(
                _0x2bc617,
                _0xcd8cae,
                _0x3dcd98,
                _0x5e2ca6,
                _0x204b6b,
                _0x496fa9,
                _0x1bcda1,
              ),
              false
            );
          },
        }));
    returnfalse;
  }
  returnfalse;
}
function acoesGridImpHtml(
  _0x3b4a7b,
  _0x1a74a3,
  _0x4dde25,
  _0x315b08,
  _0x1c3f20,
  _0x4e75ff,
  _0x213b55,
) {
  var _0x3fc120 = $("#" + _0x3b4a7b),
    _0x19172f = "<form><table>",
    _0xc5a968 = _0x1a74a3.split("---"),
    _0x4a6e59 = -0x1,
    _0x302c6e = "";
  ($.each(_0xc5a968, function (_0x2dedaf, _0x42c9c7) {
    if (!isempty(_0x42c9c7)) {
      var _0x4af30a = "",
        _0x44902d = "",
        _0x1f404f = "",
        _0x389235 = true,
        _0x295e37 = true,
        _0x528525 = _0x42c9c7.split(/\n|\r\n/);
      ($.each(_0x528525, function (_0x30fd4f, _0x520d05) {
        _0x520d05 = trim(_0x520d05);
        if (!isempty(_0x520d05)) {
          var _0x520d05 = _0x520d05.match(/(.*) ?\[([\s\S]*)\]/i);
          if (!_0x520d05) returntrue;
          ($.trim(_0x520d05[0x1]) == "colunaExibir" &&
            isempty($.trim(_0x520d05[0x2])) &&
            (_0x389235 = false),
            $.trim(_0x520d05[0x1]) == "colunaValor" &&
              (_0x4af30a = $.trim(_0x520d05[0x2])),
            $.trim(_0x520d05[0x1]) == "colunaValor64" &&
              (_0x44902d = $.trim(_0x520d05[0x2])),
            $.trim(_0x520d05[0x1]) == "colunaTitulo" &&
              (_0x1f404f = $.trim(_0x520d05[0x2])),
            $.trim(_0x520d05[0x1]) == "colunaMarcado" &&
              (_0x295e37 = $.trim(_0x520d05[0x2])));
        }
      }),
        !isempty(_0x1f404f) &&
          (_0x389235 || _0x295e37) &&
          (isempty(_0x1f404f) && (_0x1f404f = _0x4af30a),
          _0x389235 &&
            (_0x4a6e59++,
            (_0x4a6e59 == 0x0 || _0x4a6e59 % 0x4 == 0x0) &&
              (_0x4a6e59 % 0x4 == 0x0 && (_0x19172f += "</tr>"),
              (_0x19172f += "<tr>")),
            _0x295e37
              ? ((_0x302c6e += "&" + _0x44902d + "=1"),
                (_0x19172f +=
                  '<td><input type="checkbox" name="' +
                  _0x44902d +
                  '" value="1" checked="checked" onclick="impSelecionar(\'' +
                  _0x3b4a7b +
                  "', this);\" /> " +
                  _0x1f404f +
                  "</td>"))
              : (_0x19172f +=
                  '<td><input type="checkbox" name="' +
                  _0x44902d +
                  '" value="1" onclick="impSelecionar(\'' +
                  _0x3b4a7b +
                  "', this);\" /> " +
                  _0x1f404f +
                  "</td>"))));
    }
  }),
    (_0x19172f += "</table></form>"));
  var _0x329d2f,
    _0x5c6aa4 = function (_0x5ef44b) {
      $.ajax({
        url: "/sistema/personalizacaoGrid/form_func-ajax/listarSelecao/",
        dataType: "jsonp",
        data:
          "ajax=true&md5=" +
          encodeURIComponent(_0x3fc120.data("md5")) +
          "&nome=" +
          encodeURIComponent(_0x5ef44b),
        type: "get",
        success: function (_0x4be8aa) {
          var _0x4ca86a = $(".ui-dialog-content:last");
          (_0x4ca86a.find("#selecaoSalvos").remove(),
            isempty(_0x5ef44b)
              ? $(".ui-dialog-buttonset:last").find("button:eq(3)").hide()
              : $(".ui-dialog-buttonset:last").find("button:eq(3)").show(),
            _0x4be8aa.code &&
              ((_0x329d2f = _0x4be8aa.data),
              $(
                '<tr id="selecaoSalvos"><td colspan="10" style="padding: 0 10px 9px 6px;">Relatórios: ' +
                  _0x4be8aa.html +
                  "</td></tr>",
              ).prependTo(_0x4ca86a.find("table")),
              _0x4ca86a.find("#selecaoSalvos select").change(function () {
                if (!isempty($(this).val()))
                  $(".ui-dialog-buttonset:last").find("button:eq(3)").show();
                else
                  return (
                    _0x4ca86a.find("p:first").html(_0x19172f),
                    _0x5c6aa4(),
                    true
                  );
                (_0x4ca86a
                  .find('input[type="checkbox"]')
                  .prop("checked", false),
                  $.each(
                    _0x329d2f[$(this).val()],
                    function (_0x2f88f4, _0x447c95) {
                      _0x4ca86a
                        .find(
                          'input[type="checkbox"][name="' + _0x447c95 + '"]',
                        )
                        .prop("checked", true);
                    },
                  ));
              })));
        },
      });
    };
  if (_0x315b08) {
    if (!_0x1c3f20)
      return (
        abrirJanela(
          false,
          _0x3fc120.data("divIdRoot"),
          _0x3b4a7b,
          unique(),
          "",
          "Enviar um Email",
          "/etc/etc006/content/etc006ContentEmailEnviar/",
          "&sendGriHTML=" + encodeURIComponent(_0x302c6e),
          [0x384, 0x226],
        ),
        false
      );
    var _0x4182f3 = {
      "Enviar via E-mail": function () {
        (abrirJanela(
          false,
          _0x3fc120.data("divIdRoot"),
          _0x3b4a7b,
          unique(),
          "",
          "Enviar um Email",
          "/etc/etc006/content/etc006ContentEmailEnviar/",
          "&sendGriHTML=" +
            encodeURIComponent(
              $(this).closest(".ui-dialog").find("form").serialize(),
            ),
          [0x384, 0x226],
        ),
          $(this).dialog("destroy").remove());
      },
      Fechar: function () {
        $(this).dialog("destroy").remove();
      },
    };
  } else {
    if (!_0x1c3f20) {
      if (_0x4e75ff)
        (_0x3fc120.showLoading(),
          $.ajax({
            url: "/sistema/funcoes/gerar/pdf/",
            dataType: "html",
            data:
              "ajax=true&link=" +
              encodeURIComponent(
                _0x3fc120.data("href") +
                  "?ajax=true&generateHTML=true&titleTab=" +
                  _0x3fc120.data("titleTab") +
                  _0x3fc120.data("vars") +
                  "&" +
                  _0x302c6e,
              ),
            success: function (_0xd77448) {
              return (
                _0x3fc120.hideLoading(),
                $("<div>")
                  .addClass("dummyTmp")
                  .css({ display: "none", position: "fixed" })
                  .html(_0xd77448)
                  .appendTo("body"),
                false
              );
            },
          }),
          $(this).dialog("destroy").remove());
      else
        _0x213b55
          ? (_0x3fc120.showLoading(),
            $.ajax({
              url: _0x3fc120.data("href"),
              dataType: "html",
              data:
                "ajax=true&gridFiltrado=true&generateCSV=true&divIdRoot=" +
                _0x3fc120.data("divIdRoot") +
                "&divIdParent=" +
                _0x3fc120.data("divIdParent") +
                "&divIdParentChain=" +
                _0x3fc120.data("divIdParentChain") +
                "&divId=" +
                _0x3fc120.data("divId") +
                "&divIdChain=" +
                _0x3fc120.data("divIdChain") +
                _0x3fc120.data("vars"),
              success: function (_0x1afc62) {
                return (
                  _0x3fc120.hideLoading(),
                  $("<div>")
                    .addClass("dummyTmp")
                    .css({ display: "none", position: "fixed" })
                    .html(_0x1afc62)
                    .appendTo("body"),
                  false
                );
              },
              error: function () {
                (_0x3fc120.hideLoading(),
                  alert(
                    "Não foi possível gerar o CSV. Tente novamente em alguns instantes ou refine os filtros.",
                  ));
              },
            }))
          : nova_janela(
              _0x3fc120.data("href") +
                "?ajax=true&generateHTML=true&titleTab=" +
                _0x3fc120.data("titleTab") +
                _0x3fc120.data("vars") +
                "&" +
                $("<div></div>").html(_0x19172f).find("form").serialize(),
              0x14,
              0x14,
              0x384,
              0x2bc,
              "no",
              "no",
              "no",
              "no",
              "yes",
              "yes",
              0x0,
            );
      returnfalse;
    }
    var _0x4182f3 = {
      Fechar: function () {
        $(this).dialog("destroy").remove();
      },
      Imprimir: function () {
        var _0x3ada7c = $("#selecaoSalvos select").val();
        typeof _0x3ada7c == "undefined" && (_0x3ada7c = "");
        if (_0x4e75ff)
          (_0x3fc120.showLoading(),
            $.ajax({
              url: "/sistema/funcoes/gerar/pdf/",
              dataType: "html",
              data:
                "ajax=true&titulo=" +
                encodeURIComponent(_0x3ada7c) +
                "&link=" +
                encodeURIComponent(
                  _0x3fc120.data("href") +
                    "?ajax=true&generateHTML=true&titleTab=" +
                    _0x3fc120.data("titleTab") +
                    _0x3fc120.data("vars") +
                    "&" +
                    $(this).closest(".ui-dialog").find("form").serialize(),
                ),
              type: "post",
              success: function (_0x46346b) {
                return (
                  _0x3fc120.hideLoading(),
                  $("<div>")
                    .addClass("dummyTmp")
                    .css({ display: "none", position: "fixed" })
                    .html(_0x46346b)
                    .appendTo("body"),
                  false
                );
              },
            }),
            $(this).dialog("destroy").remove());
        else
          _0x213b55
            ? (_0x3fc120.showLoading(),
              $.ajax({
                url: _0x3fc120.data("href"),
                dataType: "html",
                data:
                  "ajax=true&gridFiltrado=true&generateCSV=true&divIdRoot=" +
                  _0x3fc120.data("divIdRoot") +
                  "&divIdParent=" +
                  _0x3fc120.data("divIdParent") +
                  "&divIdParentChain=" +
                  _0x3fc120.data("divIdParentChain") +
                  "&divId=" +
                  _0x3fc120.data("divId") +
                  "&divIdChain=" +
                  _0x3fc120.data("divIdChain") +
                  _0x3fc120.data("vars") +
                  "&" +
                  $(this).closest(".ui-dialog").find("form").serialize(),
                type: "post",
                success: function (_0x42fe1c) {
                  return (
                    _0x3fc120.hideLoading(),
                    $("<div>")
                      .addClass("dummyTmp")
                      .css({ display: "none", position: "fixed" })
                      .html(_0x42fe1c)
                      .appendTo("body"),
                    false
                  );
                },
                error: function () {
                  (_0x3fc120.hideLoading(),
                    alert(
                      "Não foi possível gerar o CSV. Tente novamente em alguns instantes ou refine os filtros.",
                    ));
                },
              }))
            : (nova_janela(
                _0x3fc120.data("href") +
                  "?ajax=true&titulo=" +
                  encodeURIComponent(_0x3ada7c) +
                  "&generateHTML=true&titleTab=" +
                  _0x3fc120.data("titleTab") +
                  _0x3fc120.data("vars") +
                  "&" +
                  $(this).closest(".ui-dialog").find("form").serialize(),
                0x14,
                0x14,
                0x384,
                0x2bc,
                "no",
                "no",
                "no",
                "no",
                "yes",
                "yes",
                0x0,
              ),
              $(this).dialog("destroy").remove());
      },
      "Salvar relatório": function () {
        var _0x231685 = [];
        $('.ui-dialog:last input[type="checkbox"]').each(function () {
          $(this).is(":checked") && _0x231685.push($(this).attr("name"));
        });
        var _0x6ebce1 = prompt("Nome do relatório");
        typeof _0x6ebce1 == "string" &&
          (isempty(_0x6ebce1)
            ? alert("É necessário informar um nome.")
            : $.ajax({
                url: "/sistema/personalizacaoGrid/form_func-ajax/salvarSelecao/",
                dataType: "jsonp",
                data:
                  "ajax=true&md5=" +
                  encodeURIComponent(_0x3fc120.data("md5")) +
                  "&campos=" +
                  encodeURIComponent(JSON.stringify(_0x231685)) +
                  "&titulo=" +
                  encodeURIComponent(_0x6ebce1),
                type: "post",
                success: function (_0x200394) {
                  (_0x5c6aa4(_0x6ebce1), alert("O relatório foi salvo!"));
                },
              }));
      },
      "Apagar relatório": function () {
        var _0x3ba97f = $("#selecaoSalvos select").val();
        !isempty(_0x3ba97f) &&
          $.ajax({
            url: "/sistema/personalizacaoGrid/form_func-ajax/apagarSelecao/",
            dataType: "jsonp",
            data:
              "ajax=true&md5=" +
              encodeURIComponent(_0x3fc120.data("md5")) +
              "&titulo=" +
              encodeURIComponent(_0x3ba97f),
            type: "post",
            success: function (_0x13f80a) {
              (_0x5c6aa4(), alert("O relatório salvo foi apagado!"));
            },
          });
      },
    };
  }
  (dialogConfirm("Relatório", _0x19172f, _0x4182f3),
    $(".ui-dialog-buttonset:last")
      .css("width", "100%")
      .find("button")
      .css("float", "right"),
    $(".ui-dialog-buttonset:last").find("button:eq(2)").css({ float: "left" }),
    $(".ui-dialog-buttonset:last")
      .find("button:eq(3)")
      .css({ float: "left", display: "none" }),
    _0x5c6aa4());
}
function impSelecionar(_0x37b61a, _0x53baa8) {
  var _0x2bfe17 = $("#" + _0x37b61a);
  $.ajax({
    url: "/sistema/personalizacaoGrid/form_func-ajax/impSelecionar/",
    dataType: "jsonp",
    type: "post",
    data:
      "ajax=true&coluna=" +
      encodeURIComponent($(_0x53baa8).attr("name")) +
      "&valor=" +
      encodeURIComponent($(_0x53baa8).is(":checked") ? "1" : "") +
      "&pesquisaGridId=" +
      "&grid=" +
      encodeURIComponent(_0x2bfe17.data("href")) +
      _0x2bfe17.data("vars"),
    success: function (_0x10da17) {
      returnfalse;
    },
  });
}
function gridAudit(_0x5b63be, _0x295b9d, _0x1d30f8) {
  var _0x1d30f8 = typeof _0x1d30f8 == "undefined" ? "" : _0x1d30f8,
    _0x1064a8 = gridPegarDivId(_0x5b63be),
    _0x2062a5 = $("#" + _0x1064a8);
  abrirJanela(
    false,
    _0x2062a5.data("divIdRoot"),
    _0x2062a5.data("divId"),
    unique(),
    "",
    "Auditoria de inclusão/alteração/exclusão de registros",
    "/etc/etc007/content/etc007ContentAuditoria/",
    "&tabela=" +
      encodeURIComponent(_0x295b9d) +
      "&id=" +
      encodeURIComponent(_0x1d30f8),
  );
}
function trocarEmp(_0x442b5f, _0x58b8be, _0x3f6eda) {
  var _0x3f6eda = typeof _0x3f6eda == "undefined" ? "" : _0x3f6eda,
    _0x4a261c = gridPegarDivId(_0x442b5f),
    _0x5897dc = $("#" + _0x4a261c);
  abrirJanela(
    false,
    _0x5897dc.data("divIdRoot"),
    _0x5897dc.data("divId"),
    unique(),
    "",
    "Trocar para outra empresa",
    "/sistema/funcoes/trocarEmpresa/content/",
    "&tabela=" +
      encodeURIComponent(_0x58b8be) +
      "&id=" +
      encodeURIComponent(_0x3f6eda) +
      "&divRefresh=" +
      encodeURIComponent(_0x5897dc.data("divIdParent")),
    [0x226, 0x8c],
  );
}
function voltarId(_0x20352a, _0x369d3c, _0x4c7d2b, _0x26d3f9) {
  var _0x369d3c = typeof _0x369d3c == "undefined" ? "" : _0x369d3c,
    _0x2ee076 = _0x20352a.find("form:first").attr("navSql"),
    _0x3d3010 = _0x20352a.find("form:first").attr("orderSql");
  $.getJSON(
    "/sistema/funcoes/controleFormulario/idAnterior/?ajax=true&id=" +
      encodeURIComponent(_0x369d3c) +
      "&tabela=" +
      encodeURIComponent(_0x4c7d2b) +
      "&navSql=" +
      encodeURIComponent(_0x2ee076) +
      "&orderSql=" +
      encodeURIComponent(_0x3d3010) +
      "&callback=?",
    function (_0x4f8655) {
      if (_0x4f8655.code) {
        var _0x48db2c = _0x4f8655.data,
          _0x4d0cc6 = gridPegarDivId(_0x20352a);
        (divSetarAcaoId(_0x4d0cc6, _0x48db2c),
          $(_0x26d3f9).data("id", _0x48db2c),
          divRefresh(_0x4d0cc6, true));
      }
    },
  );
}
function avancarId(_0x2775d5, _0x340e6f, _0x59d0b, _0x24802a) {
  var _0x340e6f = typeof _0x340e6f == "undefined" ? "" : _0x340e6f,
    _0x2e9af5 = _0x2775d5.find("form:first").attr("navSql"),
    _0x464dd = _0x2775d5.find("form:first").attr("orderSql");
  $.getJSON(
    "/sistema/funcoes/controleFormulario/idProximo/?ajax=true&id=" +
      encodeURIComponent(_0x340e6f) +
      "&tabela=" +
      encodeURIComponent(_0x59d0b) +
      "&navSql=" +
      encodeURIComponent(_0x2e9af5) +
      "&orderSql=" +
      encodeURIComponent(_0x464dd) +
      "&callback=?",
    function (_0x30b20c) {
      if (_0x30b20c.code) {
        var _0x4fd9d5 = _0x30b20c.data,
          _0x407a00 = gridPegarDivId(_0x2775d5);
        (divSetarAcaoId(_0x407a00, _0x4fd9d5),
          $(_0x24802a).data("id", _0x4fd9d5),
          divRefresh(_0x407a00, true));
      }
    },
  );
}
function primeiroId(_0x113b44, _0x395f4a, _0x589b51, _0x3da8d7) {
  var _0x395f4a = typeof _0x395f4a == "undefined" ? "" : _0x395f4a,
    _0x147d94 = _0x113b44.find("form:first").attr("navSql"),
    _0x5c5343 = _0x113b44.find("form:first").attr("orderSql");
  $.getJSON(
    "/sistema/funcoes/controleFormulario/idPrimeiro/?ajax=true&id=" +
      encodeURIComponent(_0x395f4a) +
      "&tabela=" +
      encodeURIComponent(_0x589b51) +
      "&navSql=" +
      encodeURIComponent(_0x147d94) +
      "&orderSql=" +
      encodeURIComponent(_0x5c5343) +
      "&callback=?",
    function (_0x5711e1) {
      if (_0x5711e1.code) {
        var _0x42b7a2 = _0x5711e1.data,
          _0x3e1a64 = gridPegarDivId(_0x113b44);
        (divSetarAcaoId(_0x3e1a64, _0x42b7a2),
          $(_0x3da8d7).data("id", _0x42b7a2),
          divRefresh(_0x3e1a64, true));
      }
    },
  );
}
function ultimoId(_0x1dd36d, _0x5d34be, _0x32b32a, _0xc22b33) {
  var _0x5d34be = typeof _0x5d34be == "undefined" ? "" : _0x5d34be,
    _0x46b040 = _0x1dd36d.find("form:first").attr("navSql"),
    _0x3c6603 = _0x1dd36d.find("form:first").attr("orderSql");
  $.getJSON(
    "/sistema/funcoes/controleFormulario/idUltimo/?ajax=true&id=" +
      encodeURIComponent(_0x5d34be) +
      "&tabela=" +
      encodeURIComponent(_0x32b32a) +
      "&navSql=" +
      encodeURIComponent(_0x46b040) +
      "&orderSql=" +
      encodeURIComponent(_0x3c6603) +
      "&callback=?",
    function (_0x657825) {
      if (_0x657825.code) {
        var _0xe5e2e3 = _0x657825.data,
          _0x552f6e = gridPegarDivId(_0x1dd36d);
        (divSetarAcaoId(_0x552f6e, _0xe5e2e3),
          $(_0xc22b33).data("id", _0xe5e2e3),
          divRefresh(_0x552f6e, true));
      }
    },
  );
}
function gridRefresh(_0x305918) {
  return (divRefresh(gridPegarDivId(_0x305918), true), false);
}
function gridPegarDivId(_0x5ba8fb) {
  var _0x9af6b5 = $(_0x5ba8fb),
    _0x93d622 = false;
  _0x9af6b5.closest(".gridOptTop").size() != 0x0 &&
    (_0x9af6b5 = _0x9af6b5.closest(".gridOptTop"));
  if (_0x9af6b5.parent().hasClass("ui-tabs-panel"))
    var _0x93d622 = _0x9af6b5.parent().attr("id");
  else {
    if (_0x9af6b5.closest(".content-2columnsOrcamento").size() != 0x0)
      var _0x93d622 = _0x9af6b5
        .closest(".content-2columnsOrcamento")
        .attr("id");
    else {
      if (_0x9af6b5.closest(".content-2columnsPedido").size() != 0x0)
        var _0x93d622 = _0x9af6b5.closest(".content-2columnsPedido").attr("id");
      else {
        if (_0x9af6b5.closest(".content-2rows").size() != 0x0)
          var _0x93d622 = _0x9af6b5.closest(".content-2rows").attr("id");
        else {
          if (_0x9af6b5.closest(".content-3rows").size() != 0x0)
            var _0x93d622 = _0x9af6b5.closest(".content-3rows").attr("id");
          else {
            if (_0x9af6b5.closest(".ui-tabs-panel").size() != 0x0)
              var _0x93d622 = _0x9af6b5.closest(".ui-tabs-panel").attr("id");
            else {
              if (_0x9af6b5.closest(".content").size() != 0x0)
                var _0x93d622 = _0x9af6b5.closest(".content").attr("id");
              else
                var _0x93d622 = _0x9af6b5
                  .closest(".ui-dialog-content")
                  .attr("id");
            }
          }
        }
      }
    }
  }
  return _0x93d622;
}
function carregaMenu() {
  ($(".menuvertical a").bind("mouseover", function (_0x185135) {
    var _0x575534 = $("#topoMenu #barra li a.hover");
    if (!(_0x185135.originalEvent instanceof MouseEvent)) {
      if (_0x575534.size() != 0x0)
        return (_0x185135.stopPropagation(), _0x185135.preventDefault(), true);
      (clearTimeout(menuKeyboardTimeout),
        (menuKeyboardTimeout = setTimeout(function () {
          menuKeyboardTimeout = false;
        }, 0x1388)));
    }
    var _0x581f4e = $(this);
    menuOut = false;
    if (_0x581f4e.parent().hasClass("menuvertical"))
      ($("ul.menu:visible").size() > 0x0 &&
        ($("#fixMenu").remove(),
        $("ul.menu").hide(),
        $(".menuvertical a").removeClass("ahover")),
        !_0x581f4e.hasClass("hover") &&
          ($("#topoMenu #barra li a.hover").removeClass("hover"),
          _0x581f4e.addClass("hover"),
          _0x581f4e.addClass("ahover")),
        _0x581f4e.parent().find("ul").show(),
        $("#fixMenu").size() == 0x0 &&
          $("<div>")
            .attr("id", "fixMenu")
            .width(_0x581f4e.parent().find("a").outerWidth() - 0x1)
            .height(0x3)
            .css({
              position: "absolute",
              "z-index": "9001",
              "margin-left": "0",
              "margin-top": "25px",
            })
            .prependTo(_0x581f4e.parent()));
    else
      _0x581f4e.parent().hasClass("submenu") &&
        !_0x581f4e.hasClass("hover") &&
        ($("#topoMenu #barra li a.hover").removeClass("hover"),
        _0x581f4e.addClass("hover"));
  }),
    $(".menuvertical a").bind("mouseout", function (_0x289260) {
      if (_0x289260.originalEvent instanceof MouseEvent && menuKeyboardTimeout)
        return (_0x289260.stopPropagation(), _0x289260.preventDefault(), true);
      ($("#topoMenu #barra li a.hover").removeClass("hover"), (menuOut = true));
      var _0x444b24 = this;
      setTimeout(function () {
        menuOut &&
          ($(_0x444b24).closest(".menuvertical").find("ul").hide(),
          $(_0x444b24).closest(".menuvertical").find("a").removeClass("ahover"),
          $("#fixMenu").remove());
      }, 0x32);
    }),
    $(".menuvertical a").bind("click", function () {}));
}
function carregaMenuMobile() {
  ($("#barra").bind("scroll", function (_0x210567) {
    ($(".menuvertical .menu").hide(),
      $(".ahover, .hover").removeClass("ahover hover").addClass("no-hover"));
  }),
    $(".menuvertical a").bind("click mouseover", function (_0x49c093) {
      var _0x3b2762 = $(this);
      menuOut = false;
      if (_0x3b2762.parent().hasClass("menuvertical")) {
        $("ul.menu:visible").size() > 0x0 &&
          ($("ul.menu").hide(),
          $(".menuvertical a").removeClass("ahover hover"));
        !_0x3b2762.hasClass("hover") &&
          ($("#topoMenu #barra li a.hover").removeClass("hover"),
          _0x3b2762.addClass("hover"),
          _0x3b2762.addClass("ahover"));
        var _0x177f44 = _0x3b2762.parent().find("ul");
        _0x177f44
          .css({
            top:
              _0x3b2762.offset().top +
              _0x3b2762.outerHeight() -
              getExtraVerticalSpace(_0x3b2762) / 0x2,
            left: _0x3b2762.offset().left + "px",
          })
          .show();
      } else
        _0x3b2762.parent().hasClass("submenu") &&
          !_0x3b2762.hasClass("hover") &&
          ($("#topoMenu #barra li a.hover").removeClass("hover"),
          _0x3b2762.addClass("hover"));
    }),
    $(".menuvertical a").bind("mouseout", function (_0x515282) {
      if (_0x515282.originalEvent instanceof MouseEvent && menuKeyboardTimeout)
        return (_0x515282.stopPropagation(), _0x515282.preventDefault(), true);
      ($("#topoMenu #barra li a.hover").removeClass("hover"), (menuOut = true));
      var _0x403f82 = this;
      setTimeout(function () {
        menuOut &&
          ($(_0x403f82).closest(".menuvertical").find("ul").hide(),
          $(_0x403f82)
            .closest(".menuvertical")
            .find("a")
            .removeClass("ahover"));
      }, 0x32);
    }));
}
$.widget("ui.form", {
  _init: function () {
    var _0x511bbd = this,
      _0x5f5828 = this["element"],
      _0x573c1c = _0x5f5828.find("input, select, textarea");
    (_0x573c1c.each(function () {
      var _0x47a51e = $(this);
      _0x47a51e.attr("type") != "button" &&
        _0x47a51e.attr("type") != "submit" &&
        _0x47a51e.addClass("formInput");
    }),
      _0x5f5828.find("fieldset").addClass("ui-widget-content"),
      _0x5f5828.find("legend").addClass("ui-widget-header ui-corner-all"),
      _0x5f5828.addClass("ui-widget"),
      $.each(_0x573c1c, function () {
        var _0x245ff4 = $(this);
        (_0x245ff4
          .focusin(function () {
            currentFocus = this;
          })
          .focusout(function () {
            currentFocus = null;
          }),
          _0x245ff4.addClass("ui-state-default ui-corner-all"),
          _0x245ff4.wrap("<label />"));
        if (_0x245ff4.is(":reset ,:submit")) _0x511bbd.buttons(this);
        else {
          if (_0x245ff4.is(":checkbox")) _0x511bbd.checkboxes(this);
          else {
            if (
              _0x245ff4.is("input[type='text']") ||
              _0x245ff4.is("textarea") ||
              _0x245ff4.is("input[type='password']") ||
              _0x245ff4.is("input[type='file']")
            )
              _0x511bbd.textelements(this);
            else {
              if (_0x245ff4.is(":radio")) _0x511bbd.radio(this);
              else {
                if (_0x245ff4.is("select")) _0x511bbd.selector(this);
              }
            }
          }
        }
        if (_0x245ff4.hasClass("date")) _0x245ff4.datepicker();
      }));
    var _0x3e30c7 = jQuery("<div />", {
        css: { width: 0x14, height: 0x14, margin: 0xa, textAlign: "center" },
      }).addClass("ui-state-default drag"),
      _0x38b8f2 = Math.ceil(Math.random() * 0x4),
      _0x58ebf8 = jQuery("<div />", {
        id: "droppable",
        text: "Drop the box with " + _0x38b8f2 + " here",
        css: { width: 0x64, height: 0x64, float: "right", fontWeight: "bold" },
      }).addClass("ui-state-default");
    $(_0x5f5828).find("fieldset").append(_0x58ebf8);
    for (var _0x3e953c = 0x1; _0x3e953c < 0x5; _0x3e953c++) {
      $(_0x5f5828)
        .find("fieldset")
        .append(_0x3e30c7.clone().html(_0x3e953c).attr("id", _0x3e953c));
    }
    ($(".drag").draggable({ containment: "parent" }),
      $("#droppable").droppable({
        accept: "#" + _0x38b8f2,
        drop: function (_0x35c617, _0x418301) {
          ($(this).addClass("ui-state-highlight").html("Right One"),
            _0x5f5828
              .find(":submit")
              .removeClass("ui-state-disabled")
              .unbind("click"));
        },
      }),
      $(".input-hover").hover(
        function () {
          $(this).addClass("ui-state-hover");
        },
        function () {
          $(this).removeClass("ui-state-hover");
        },
      ));
  },
  textelements: function (_0x50ff1b) {
    var _0x41c64f = $(_0x50ff1b),
      _0x308933 = false;
    _0x41c64f.hasClass("form-input-error") &&
      ((_0x308933 = true),
      _0x41c64f.removeClass("state-default").addClass("ui-state-error"),
      _0x41c64f
        .parent()
        .addClass("ui-state-error")
        .css({ background: "none", border: "none" }));
    if (_0x41c64f.hasClass("campo_input_pesquisa")) {
      var _0x1287c3 = 0x2,
        _0x4d3659 = 0x13;
      (device == "mobile" && ((_0x1287c3 = 0x9), (_0x4d3659 = 0x14)),
        $("<span>")
          .addClass("ui-icon ui-icon-search")
          .attr("title", "Digite no campo para pesquisar")
          .css({
            "margin-left": "-16px",
            position: "relative",
            float: "left",
            top: _0x1287c3 + "px",
            left: _0x4d3659 + "px",
          })
          .prependTo(_0x41c64f.parent()),
        _0x41c64f.css("width", _0x41c64f.width() + "px"),
        device == "mobile"
          ? _0x41c64f[0x0].style.setProperty(
              "padding-left",
              "18px",
              "important",
            )
          : _0x41c64f.css("padding-left", "17px"),
        _0x41c64f.unbind("keypress"),
        _0x41c64f.bind("keypress", function (_0x2facad) {
          if (_0x2facad.keyCode == 0xd) {
            if (!isempty(_0x41c64f.closest("form").data("func")))
              return (window[_0x41c64f.closest("form").data("func")](), false);
          }
        }));
    } else {
      if (
        _0x41c64f.hasClass("campo_input_pesquisa_grid") &&
        !_0x41c64f.hasClass("campo_input_pesquisa_grid_ok")
      ) {
        var _0x1287c3 = 0x2,
          _0x4d3659 = 0x13;
        (device == "mobile" && ((_0x1287c3 = 0x9), (_0x4d3659 = 0x14)),
          $("<span>")
            .attr("title", "Aperte ENTER dentro do campo para pesquisar")
            .addClass("ui-icon ui-icon-calculator")
            .css({
              "margin-left": "-17px",
              position: "relative",
              float: "left",
              top: _0x1287c3 + "px",
              left: _0x4d3659 + "px",
            })
            .prependTo(_0x41c64f.parent()),
          _0x41c64f
            .addClass("campo_input_pesquisa_grid_ok")
            .css("width", _0x41c64f.width() + "px"),
          device == "mobile"
            ? _0x41c64f[0x0].style.setProperty(
                "padding-left",
                "18px",
                "important",
              )
            : _0x41c64f.css("padding-left", "17px"));
      } else
        !_0x41c64f.is("textarea") &&
          (!_0x41c64f.hasClass("temAcaoEnter")
            ? _0x41c64f.unbind("keypress")
            : ($("<span>")
                .addClass("ui-icon ui-icon-newwin")
                .css({
                  "margin-left": "-17px",
                  position: "relative",
                  float: "left",
                  top: "2px",
                  left: "19px",
                })
                .prependTo(_0x41c64f.parent()),
              _0x41c64f.css({
                "padding-left": "17px",
                width: _0x41c64f.width() + "px",
              })),
          _0x41c64f.bind("keypress", function (_0x20a8d2) {
            if (
              _0x20a8d2.keyCode == 0xd &&
              !_0x41c64f.hasClass("temAcaoEnter")
            ) {
              if (_0x41c64f.hasClass("input-editar-campo"))
                (_0x41c64f.removeClass("campo_input_data"),
                  _0x41c64f.removeClass("campo_input_datahora"),
                  _0x41c64f.trigger("blur"));
              else {
                if (!isempty(_0x41c64f.closest("form").data("func")))
                  return (
                    window[_0x41c64f.closest("form").data("func")](),
                    false
                  );
              }
            } else {
              if (_0x20a8d2.keyCode == 0x9) {
                if (_0x41c64f.hasClass("input-editar-campo")) {
                  (_0x41c64f.trigger("blur"),
                    _0x41c64f.removeClass("campo_input_data"),
                    _0x41c64f.removeClass("campo_input_datahora"));
                  var _0x352dce = _0x41c64f.closest("td"),
                    _0x4b681f = _0x352dce.parent().children().index(_0x352dce);
                  return (
                    _0x20a8d2.shiftKey
                      ? (setTimeout(function () {
                          _0x352dce
                            .parent()
                            .prev()
                            .find("td:eq(" + _0x4b681f + ")")
                            .trigger("click");
                        }, 0x64),
                        setTimeout(function () {
                          _0x352dce
                            .parent()
                            .prev()
                            .find("td:eq(" + _0x4b681f + ")")
                            .find("input")
                            .focus();
                        }, 0xc8))
                      : (setTimeout(function () {
                          _0x352dce
                            .parent()
                            .next()
                            .find("td:eq(" + _0x4b681f + ")")
                            .trigger("click");
                        }, 0x64),
                        setTimeout(function () {
                          _0x352dce
                            .parent()
                            .next()
                            .find("td:eq(" + _0x4b681f + ")")
                            .find("input")
                            .focus();
                        }, 0xc8)),
                    false
                  );
                }
              }
            }
          }));
    }
    $(_0x50ff1b).bind({
      focusin: function () {
        $(this).toggleClass("ui-state-focus");
      },
      focusout: function () {
        $(this).toggleClass("ui-state-focus");
      },
    });
  },
  buttons: function (_0x248317) {},
  checkboxes: function (_0x4a794a) {},
  radio: function (_0x51bbf0) {
    returnfalse;
    if (!$(_0x51bbf0).hasClass("noStyle")) {
      $(_0x51bbf0).parent("label").after("<span />");
      var _0x553f34 = $(_0x51bbf0).parent("label").next();
      ($(_0x51bbf0).addClass("ui-helper-hidden"),
        _0x553f34.addClass("ui-icon ui-icon-radio-off"),
        _0x553f34.wrap(
          "<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>",
        ),
        _0x553f34.parent().addClass("input-hover"),
        _0x553f34.parent("span").click(function (_0x37eedf) {
          ($(this).toggleClass("ui-state-active"),
            _0x553f34.toggleClass("ui-icon-radio-off ui-icon-bullet"),
            $(_0x51bbf0).click());
        }));
    }
  },
  selector: function (_0x50b13e) {
    var _0x3ec0ec = $(_0x50b13e);
    if (device == "mobile") {
      const _0x2f46c3 = _0x3ec0ec.parent();
      _0x2f46c3.css({ position: "relative" });
      const _0xc185e4 = _0x2f46c3.siblings(".ui-icon-refresh");
      _0x2f46c3.prepend(_0xc185e4);
    }
    var _0x3b6a8d = false;
    (_0x3ec0ec.hasClass("form-input-error") &&
      ((_0x3b6a8d = true),
      _0x3ec0ec.removeClass("state-default").addClass("ui-state-error"),
      _0x3ec0ec
        .parent()
        .addClass("ui-state-error")
        .css({ background: "none", border: "none" })),
      _0x3ec0ec.unbind("keypress"),
      _0x3ec0ec.bind("keypress", function (_0x5a4b80) {
        if (_0x5a4b80.keyCode == 0xd) {
          if (!isempty(_0x3ec0ec.closest("form").data("func")))
            return (window[_0x3ec0ec.closest("form").data("func")](), false);
        }
      }));
  },
});
function nova_janela(
  _0x2632e9,
  _0x360671,
  _0x44bb97,
  _0x562b64,
  _0x73fe0,
  _0x3f9e3b,
  _0x1ececb,
  _0x1196a7,
  _0x10150a,
  _0x24737a,
  _0x5c756a,
  _0x7ba127,
) {
  if (!_0x3f9e3b) var _0x3f9e3b = "yes";
  if (!_0x24737a) var _0x24737a = "yes";
  if (!_0x5c756a) var _0x5c756a = "yes";
  if (!_0x10150a) var _0x10150a = "yes";
  ((param =
    "menubar=" +
    _0x5c756a +
    ",toolbar=" +
    _0x1ececb +
    ",location=" +
    _0x1196a7 +
    ",status=" +
    _0x10150a +
    ",scrollbars=" +
    _0x24737a +
    ",resizable=" +
    _0x3f9e3b +
    ",width=" +
    _0x562b64 +
    ",height=" +
    _0x73fe0 +
    ",top=" +
    _0x360671 +
    ",left=" +
    _0x44bb97),
    window.open(_0x2632e9, _0x7ba127, param));
}
$.fn.limpaForm = function () {
  return this["each"](function () {
    var _0x3da1a4 = this["type"],
      _0x3b4987 = this["tagName"].toLowerCase();
    if (_0x3b4987 == "form") return $(":input", this).limpaForm();
    if (
      _0x3da1a4 == "text" ||
      _0x3da1a4 == "password" ||
      _0x3b4987 == "textarea"
    )
      this["value"] = "";
    else {
      if (_0x3da1a4 == "checkbox" || _0x3da1a4 == "radio")
        this["checked"] = false;
      else {
        if (_0x3b4987 == "select") this["selectedIndex"] = 0x0;
      }
    }
  });
};
function setCookie(_0x13f40e, _0x1344e4, _0x588bad, _0x34504f) {
  var _0x34504f = typeof _0x34504f == "undefined" ? true : false,
    _0x582d19 = new Date();
  _0x582d19.setTime(_0x582d19.getTime() + _0x588bad * 0x3e8);
  if (_0x34504f)
    var _0x94147a =
      escape(_0x1344e4) +
      (_0x588bad == null ? "" : "; expires=" + _0x582d19.toUTCString());
  else var _0x94147a = escape(_0x1344e4);
  document.cookie = _0x13f40e + "=" + _0x94147a + "; path=/";
}
function getCookie(_0x3be8b1) {
  var _0x40e5b2,
    _0x52eb32,
    _0x54e35b,
    _0x486cac = document.cookie.split(";");
  for (_0x40e5b2 = 0x0; _0x40e5b2 < _0x486cac.length; _0x40e5b2++) {
    ((_0x52eb32 = _0x486cac[_0x40e5b2].substr(
      0x0,
      _0x486cac[_0x40e5b2].indexOf("="),
    )),
      (_0x54e35b = _0x486cac[_0x40e5b2].substr(
        _0x486cac[_0x40e5b2].indexOf("=") + 0x1,
      )),
      (_0x52eb32 = _0x52eb32.replace(/^\s+|\s+$/g, "")));
    if (_0x52eb32 == _0x3be8b1) return unescape(_0x54e35b);
  }
}
function fontIC(_0x9c8e1c) {
  var _0x9c8e1c = typeof _0x9c8e1c == "undefined" ? false : _0x9c8e1c;
  _0x9c8e1c &&
    ($("*").removeData("fontChange"),
    setCookie("fontSize", _0x9c8e1c, 0x0, true),
    fontICIcon(_0x9c8e1c));
  var _0x589b46 = getCookie("fontSize");
  if (!_0x589b46) returnfalse;
  if (!$("body").data("fontChange")) {
    var _0x2b983d = parseInt($("body").css("font-size").replace(/px$/, ""));
    ($("body").css("font-size", _0x589b46 + "px"),
      $("body").data("fontChange", true));
  }
  return (
    $(".ui-widget").each(function () {
      !$(this).data("fontChange") &&
        ($(this).css("font-size", _0x589b46 - 0x1 + "px"),
        $(this).data("fontChange", true));
    }),
    true
  );
}
function fontICIcon(_0x5eebc5) {
  $("#fontSizeIcon").html("");
  if (_0x5eebc5 == 0xc)
    $("<img>")
      .attr({
        src: "/hardness3/static/img/icon-font-decrease.png",
        title: "Diminuir tamanho da fonte.",
      })
      .css({
        cursor: "pointer",
        width: "16px",
        height: "16px",
        position: "relative",
        top: "3px",
      })
      .click(function () {
        fontIC(0xb);
      })
      .appendTo($("#fontSizeIcon"));
  else {
    if (_0x5eebc5 == 0xb)
      ($("<img>")
        .attr({
          src: "/hardness3/static/img/icon-font-increase.png",
          title: "Aumentar tamanho da fonte.",
        })
        .css({
          cursor: "pointer",
          width: "16px",
          height: "16px",
          position: "relative",
          top: "3px",
        })
        .click(function () {
          fontIC(0xc);
        })
        .appendTo($("#fontSizeIcon")),
        $("<img>")
          .attr({
            src: "/hardness3/static/img/icon-font-decrease.png",
            title: "Diminuir tamanho da fonte.",
          })
          .css({
            cursor: "pointer",
            width: "16px",
            height: "16px",
            position: "relative",
            top: "3px",
          })
          .click(function () {
            fontIC(0xa);
          })
          .appendTo($("#fontSizeIcon")));
    else
      _0x5eebc5 == 0xa &&
        $("<img>")
          .attr({
            src: "/hardness3/static/img/icon-font-increase.png",
            title: "Aumentar tamanho da fonte.",
          })
          .css({
            cursor: "pointer",
            width: "16px",
            height: "16px",
            position: "relative",
            top: "3px",
          })
          .click(function () {
            fontIC(0xb);
          })
          .appendTo($("#fontSizeIcon"));
  }
}
(function (_0x10084b) {
  var _0x3d7061 = _0x10084b.fn.html;
  _0x10084b.fn.html = function (_0x33df85) {
    return _0x3d7061.call(this, _0x33df85);
  };
})(jQuery);
function changeEmpresaAtual(_0x26a580) {
  $.ajax({
    url: "/empresa/" + $(_0x26a580).val() + "/",
    dataType: "html",
    type: "post",
    success: function (_0x579535) {
      g.empresaAtual = $(_0x26a580).val();
      var _0x22008f = $(".ui-dialog-content:first").data("janelaPermissao");
      typeof _0x22008f != "undefined" &&
        $.getJSON(
          "/sistema/funcoes/util/verificaPermissao/?ajax=true&permissao=" +
            _0x22008f +
            "&callback=?",
          function (_0x59b14f) {
            _0x59b14f == 0x0 && (window.location = "/erp");
          },
        );
      ((_0x579535 = _0x579535.split("</head>")), (_0x579535 = _0x579535[0x1]));
      var _0x161a84 = $(_0x579535),
        _0x158b3b = $("<div></div>").html(_0x579535);
      ($(".main:first").html(_0x158b3b.find(".main").html()),
        _0x161a84.each(function () {
          var _0x39f7c8 = $(this).html();
          typeof _0x39f7c8 == "string" &&
            _0x39f7c8.match(/typeof jQuery/) &&
            _0x39f7c8.match(/esconderUsuario/) &&
            eval(_0x39f7c8);
        }),
        carregaMenu(),
        $(".ui-dialog-titlebar-close .ui-icon-closethick").trigger("click"),
        (document.title = g.nomeEmpresaAbreviado),
        divRefresh($(".content:first").attr("id")),
        $("#messageAlert .alerts").html(0x0),
        $(".msgalert").remove(),
        acoesNotificacoes(),
        setTimeout(function () {
          $.each(funcoesMensagens, function (_0xc537da, _0x1a0f61) {
            _0x1a0f61();
          });
        }, 0x1f4));
    },
  });
}
function changeUsuarioAtual(_0x188b53) {
  $.ajax({
    url: "/usuario/" + $(_0x188b53).val() + "/",
    dataType: "html",
    type: "post",
    success: function (_0x20d1bc) {
      var _0x4a066b = $(".ui-dialog-content:first").data("janelaPermissao");
      typeof _0x4a066b != "undefined" &&
        $.getJSON(
          "/sistema/funcoes/util/verificaPermissao/?ajax=true&permissao=" +
            _0x4a066b +
            "&callback=?",
          function (_0x2bc51a) {
            _0x2bc51a == 0x0 && (window.location = "/erp");
          },
        );
      ((_0x20d1bc = _0x20d1bc.split("</head>")), (_0x20d1bc = _0x20d1bc[0x1]));
      var _0x593403 = $(_0x20d1bc),
        _0x3f524b = $("<div></div>").html(_0x20d1bc);
      ($(".main:first").html(_0x3f524b.find(".main").html()),
        _0x593403.each(function () {
          var _0x4ed55a = $(this).html();
          typeof _0x4ed55a == "string" &&
            _0x4ed55a.match(/typeof jQuery/) &&
            _0x4ed55a.match(/esconderUsuario/) &&
            eval(_0x4ed55a);
        }),
        carregaMenu(),
        $('.messages [id*="empresa-"]').remove(),
        $(".ui-dialog-titlebar-close .ui-icon-closethick").trigger("click"),
        divRefresh($(".content:first").attr("id")),
        $("#messageAlert .alerts").html(0x0),
        $(".msgalert").remove(),
        acoesNotificacoes(),
        setTimeout(function () {
          $.each(funcoesMensagens, function (_0x24672e, _0x58fb63) {
            _0x58fb63();
          });
        }, 0x1f4));
    },
  });
}
function divIdRootAberto() {
  var _0x30168f = $(".content:first").data("divIdRoot");
  return (isempty(_0x30168f) && (_0x30168f = unique()), _0x30168f);
}
function abrirAgrupamento(_0x527f14) {
  var _0x2c390c = $(".content:visible:last");
  _0x2c390c.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
    (_0x2c390c = _0x2c390c.find(".ui-tabs-panel:visible:last"));
  var _0x49af04 =
      _0x2c390c.data("href") + "?ajax=true" + _0x2c390c.data("vars"),
    _0x1d80d5 = $("#" + getDivIdTab(_0x2c390c))
      .data("titleTab")
      .split("||");
  ((_0x1d80d5 = _0x1d80d5[0x1]),
    abrirJanela(
      false,
      _0x2c390c.data("divIdRoot"),
      _0x2c390c.data("divId"),
      unique(),
      "",
      "Agrupamento",
      "/sistema/funcoes/agrupamento/content/",
      "&dados=" +
        encodeURIComponent(_0x527f14) +
        "&href=" +
        encodeURIComponent(_0x49af04) +
        "&tituloGrid=" +
        encodeURIComponent(_0x1d80d5),
      [0x384, 0x1e0],
    ));
}
function importacaoDeDados(_0x45fe32) {
  ((_0x45fe32 = $("#" + gridPegarDivId(_0x45fe32))),
    abrirJanela(
      false,
      _0x45fe32.data("divIdRoot"),
      _0x45fe32.data("divId"),
      unique(),
      "",
      "Importação de Dados",
      "/sistema/funcoes/importacaoDeDados/content/",
      "&href=" +
        encodeURIComponent(_0x45fe32.data("href")) +
        _0x45fe32.data("vars"),
    ));
}
function duplicarGrid(_0x3b8b78) {
  var _0x44f2b3 = gridPegarDivId(_0x3b8b78),
    _0x39825b = $("#" + _0x44f2b3),
    _0x1ebf0d = trim(
      $(_0x3b8b78)
        .closest(".tabs:visible")
        .find(".ui-tabs-nav > li.ui-tabs-selected")
        .text(),
    ),
    _0x5ad11d = $(_0x3b8b78).closest(".tabs:visible").closest(".content");
  (!_0x1ebf0d && (_0x1ebf0d = "Grid"),
    divSetarAcaoId(_0x44f2b3, _0x1ebf0d, "duplicarGrid"),
    divSetarAcaoId(_0x44f2b3, _0x5ad11d.data("href"), "duplicarGridTabHref"),
    divRefresh(
      _0x44f2b3,
      true,
      function () {
        (divSetarAcaoId(_0x44f2b3, "", "duplicarGrid"),
          divRefresh(_0x5ad11d.data("divId"), false));
      },
      false,
    ));
}
function inclusaoMultiProdutos(_0x26c3b3) {
  ((_0x26c3b3 = $("#" + gridPegarDivId(_0x26c3b3))),
    abrirJanela(
      false,
      _0x26c3b3.data("divIdRoot"),
      _0x26c3b3.data("divId"),
      unique(),
      "",
      "Inclusão de Produtos",
      "/sistema/funcoes/inclusaoMultiProdutos/content/",
      "&href=" +
        encodeURIComponent(_0x26c3b3.data("href")) +
        _0x26c3b3.data("vars"),
    ));
}
function trim(_0x4e4c3f, _0x406cc6) {
  var _0x48f056,
    _0x193c3c = 0x0,
    _0x50f68c = 0x0;
  _0x4e4c3f += "";
  !_0x406cc6
    ? (_0x48f056 = " \n\r\tf\u000b            ​　")
    : ((_0x406cc6 += ""),
      (_0x48f056 = _0x406cc6.replace(
        /([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g,
        "$1",
      )));
  _0x193c3c = _0x4e4c3f.length;
  for (_0x50f68c = 0x0; _0x50f68c < _0x193c3c; _0x50f68c++) {
    if (_0x48f056.indexOf(_0x4e4c3f.charAt(_0x50f68c)) === -0x1) {
      _0x4e4c3f = _0x4e4c3f.substring(_0x50f68c);
      break;
    }
  }
  _0x193c3c = _0x4e4c3f.length;
  for (_0x50f68c = _0x193c3c - 0x1; _0x50f68c >= 0x0; _0x50f68c--) {
    if (_0x48f056.indexOf(_0x4e4c3f.charAt(_0x50f68c)) === -0x1) {
      _0x4e4c3f = _0x4e4c3f.substring(0x0, _0x50f68c + 0x1);
      break;
    }
  }
  return _0x48f056.indexOf(_0x4e4c3f.charAt(0x0)) === -0x1 ? _0x4e4c3f : "";
}
function temPersonalizacao(_0x93c9d6) {
  var _0x5bdcf4 = _0x93c9d6.replace(/^\//, "");
  ((_0x5bdcf4 = _0x5bdcf4.replace(/\/$/, "")),
    (_0x5bdcf4 = _0x5bdcf4 + "-alterarPrograma.php"),
    (_0x5bdcf4 = _0x5bdcf4.replace(/\//g, "-")));
  var _0x27757a = "";
  return (
    $.each(g.personalizacaoConf, function (_0x1c99eb, _0x4a6dd7) {
      (_0x4a6dd7 == _0x5bdcf4 || _0x4a6dd7 == g.empresaAtual + _0x5bdcf4) &&
        (_0x27757a = "(P)");
    }),
    _0x27757a
  );
}
function temPersonalizacaoCallback(_0xae0538) {
  var _0x71dff5 = trim(_0xae0538, "()") + "-alterarCallback.php";
  _0x71dff5 = _0x71dff5.replace(/\//g, "-");
  var _0x3482f5 = "(P)";
  return (
    $.each(g.personalizacaoConf, function (_0x4a8baf, _0x3728c2) {
      (_0x3728c2 == _0x71dff5 || _0x3728c2 == g.empresaAtual + _0x71dff5) &&
        (_0x3482f5 = "(P)");
    }),
    _0x3482f5
  );
}
function strpos(_0x34bd3c, _0x125d3c, _0xeec3c4) {
  var _0x6ec123 = (_0x34bd3c + "").indexOf(_0x125d3c, _0xeec3c4 || 0x0);
  return _0x6ec123 === -0x1 ? false : _0x6ec123;
}
function carregarTotais(_0x33ecbf) {
  var _0x4517af = $("#" + _0x33ecbf);
  $.ajax({
    url: _0x4517af.data("href"),
    dataType: "html",
    data:
      "ajax=true" +
      "&divId=" +
      encodeURIComponent(_0x4517af.data("divId")) +
      "&divIdChain=" +
      encodeURIComponent(_0x4517af.data("divIdChain")) +
      "&divIdParent=" +
      encodeURIComponent(_0x4517af.data("divIdParent")) +
      "&divIdParentChain=" +
      encodeURIComponent(_0x4517af.data("divIdParentChain")) +
      "&divIdRoot=" +
      encodeURIComponent(_0x4517af.data("divIdRoot")) +
      _0x4517af.data("vars") +
      "&exibirTotais=true",
    success: function (_0x56a174) {
      var _0x57df88 = $("<div></div>").html(_0x56a174);
      (_0x4517af
        .find(".exibirTotais")
        .html(_0x57df88.find(".buttonsBottomTop").html()),
        reloadUI(_0x33ecbf, true, "exibirTotais"));
    },
  });
}
function alertAdicionar(
  _0x1af15e,
  _0x3d0c65,
  _0x4bf71e,
  _0x373bb0,
  _0x2efdec,
  _0xa40ba6,
  _0x6a8d6b,
  _0x13ae9d,
  _0x8fe90b,
  _0x6e618,
) {
  var _0xa40ba6 = typeof _0xa40ba6 == "undefined" ? "" : _0xa40ba6,
    _0x6a8d6b = typeof _0x6a8d6b == "undefined" ? true : _0x6a8d6b,
    _0x13ae9d = typeof _0x13ae9d == "undefined" ? "" : _0x13ae9d,
    _0x373bb0 = typeof _0x373bb0 == "undefined" ? "" : _0x373bb0,
    _0x8fe90b = typeof _0x8fe90b == "undefined" ? false : _0x8fe90b,
    _0x107209 = $("#messageAlert .messages"),
    _0xa18766 = $("#messageAlert .alerts"),
    _0x5d86e1 = $(".alertsSuporte"),
    _0x10c17e = _0x4bf71e,
    _0x5d063a = _0x4bf71e;
  _0x373bb0 && (_0x10c17e = _0x10c17e + "-" + _0x373bb0);
  _0x1af15e &&
    ((_0x10c17e = _0x10c17e + "-" + _0x1af15e),
    (_0x5d063a = _0x5d063a + "-" + _0x1af15e));
  var _0x2ec5bc = new Date().getTime() / 0x64;
  if (
    !isset(alertasRemovidos[_0x10c17e]) ||
    _0x2ec5bc > alertasRemovidos[_0x10c17e][0x0] + 0x78
  ) {
    var _0x942152 = $("#messageAlert .alerts").data("piscando");
    (typeof _0x942152 == "undefined" || _0x942152 == 0x0) &&
      ($("#messageAlert .alerts").css("font-size", "14px"),
      $("#messageAlert .alerts").data("piscando", 0x1),
      pulsate());
  } else
    isset(alertasRemovidos[_0x10c17e]) &&
      (_0x13ae9d = alertasRemovidos[_0x10c17e][0x1]);
  _0x107209.find("li.alertid" + _0x10c17e).remove();
  var _0x1e5186 = "";
  _0x10c17e.match(/^chat-/) && (_0x1e5186 = " alertaidchat");
  _0x373bb0 != "" &&
    _0x373bb0.match(/^alerta-/) &&
    (_0x1e5186 = " alertaNotificacao");
  if (_0x10c17e.match(/^suporte-/))
    return (
      (_0x1e5186 = " alertaSuporte"),
      $.inArray(_0x10c17e, notificacoesSuporte) == -0x1 &&
        (notificacoesSuporte.push(_0x10c17e),
        _0x5d86e1.html(parseInt(_0x5d86e1.html()) + 0x1).fadeIn()),
      true
    );
  var _0x1a9d9e,
    _0x10fdb1,
    _0x3d0c9f,
    _0x5dc390 = "",
    _0x5ae880;
  !_0x8fe90b
    ? ((_0x8fe90b = "0"),
      (_0x1a9d9e = "flex"),
      (_0x10fdb1 = "/hardness3/static/img/remove.png"),
      (_0x5ae880 = "Desabilitar notificação"),
      (_0x3d0c9f = "none"))
    : ((_0x8fe90b = "1"),
      (_0x1a9d9e = "none"),
      (_0x10fdb1 = "/hardness3/static/img/check.png"),
      (_0x5dc390 = " notifOculta"),
      (_0x3d0c9f = "inline"),
      (_0x5ae880 = "Habilitar notificação"));
  var _0x26bdae = $(
    '<li style="display: ' +
      _0x1a9d9e +
      ";" +
      _0x13ae9d +
      '" horainclusao="' +
      _0x2ec5bc +
      '"></li>',
  )
    .addClass(
      "msgalert alertidroot" +
        _0x5d063a +
        " alertid" +
        _0x10c17e +
        _0x1e5186 +
        _0x5dc390,
    )
    .data("idRemover", _0x10c17e);
  if (_0x6e618 == "false")
    var _0x261bbe = $('<div style="width: 100%;"></div>')
      .html(_0x2efdec)
      .data("mensagem", _0x2efdec)
      .appendTo(_0x26bdae);
  else
    var _0x261bbe = $('<div style="width: 100%;"></div>')
      .html(_0x2efdec)
      .data("mensagem", _0x2efdec)
      .append(
        '<img class="iconeExibirOcultarNotificacao" src="' +
          _0x10fdb1 +
          '" onclick="exibirOcultarNotificacao($(this), \'' +
          _0x10c17e +
          "', '" +
          _0x8fe90b +
          '\'); event.stopPropagation();" style="-webkit-filter: opacity(70%); filter: opacity(70%); width: 15px; float: right; display: ' +
          _0x3d0c9f +
          ';" title="' +
          _0x5ae880 +
          '">',
      )
      .appendTo(_0x26bdae);
  $("#messageAlert .messages .body").css("height", "auto");
  $("#messageAlert .messages .body").height() > $("#desktop").height() - 0x32 &&
    $("#messageAlert .messages .body").height($("#desktop").height() - 0x32);
  (_0x26bdae.bind("mouseover", function (_0x1874ee) {
    _0x1af15e && _0x1af15e != g.empresaAtual
      ? _0x1874ee.target.nodeName != "IMG"
        ? _0x26bdae.css(
            "cursor",
            "url(/hardness3/static/img/block_cursor.png), auto",
          )
        : _0x26bdae.css("cursor", "pointer")
      : _0x26bdae.css("cursor", "pointer");
  }),
    _0x26bdae.bind("click", function () {
      if (_0x1af15e && _0x1af15e != g.empresaAtual) returnfalse;
      _0x6a8d6b &&
        ((alertasRemovidos[_0x10c17e] = []),
        (alertasRemovidos[_0x10c17e][0x0] = _0x2ec5bc),
        (alertasRemovidos[_0x10c17e][0x1] = $(this).attr("style")),
        $("#messageAlert .messages").slideUp(),
        parseInt(_0xa18766.html()) == 0x1
          ? (_0xa18766.html("0").fadeOut(),
            _0x107209.find(".body li.vazio").show(),
            $("#messageAlert .icone").css("opacity", "0.5"))
          : _0xa18766.html(parseInt(_0xa18766.html()) - 0x1).fadeIn(),
        $(this).remove());
      !isempty(_0xa40ba6) && _0xa40ba6();
      if (!_0x6a8d6b) returnfalse;
    }));
  _0x10c17e.match(/^suporte-/) && _0x26bdae.css("background-color", "#98FF8B");
  if (_0x1af15e) {
    var _0x23df46 = $("#messageAlert .messages #empresa-" + _0x1af15e);
    if (_0x23df46.size() == 0x0) {
      var _0x23df46 = $(
        '<li style="text-align: center; padding: 2px;" class="notificacaoHeader">' +
          _0x3d0c65 +
          "</li>",
      );
      _0x23df46
        .attr("id", "empresa-" + _0x1af15e)
        .appendTo(_0x107209.find(".body ul"));
    }
    _0x26bdae.insertAfter(_0x23df46);
  } else _0x26bdae.prependTo(_0x107209.find(".body ul"));
  (_0x107209.find(".body li.vazio").hide(),
    $("#messageAlert .icone").css("opacity", "1"));
}
function alertRemover(_0x3aa179, _0x314b26) {
  _0x314b26 && (_0x3aa179 = _0x3aa179 + "-" + _0x314b26);
  if (_0x3aa179.match(/^suporte-/)) {
    classe = " alertaSuporte";
    var _0x4b5f0a = $(".alertsSuporte");
    return (
      notificacoesSuporte.splice(notificacoesSuporte.indexOf(_0x3aa179)),
      _0x4b5f0a.html(parseInt(_0x4b5f0a.html()) - 0x1).fadeIn(),
      true
    );
  }
  var _0x3855b9 = $("#messageAlert .messages"),
    _0xc9ba2b = $("#messageAlert .alerts");
  _0x3855b9.find("li.alertidroot" + _0x3aa179).each(function () {
    ((idRemover = $(this).data("idRemover")),
      (alertasRemovidos[idRemover] = []),
      (alertasRemovidos[idRemover][0x0] = new Date().getTime() / 0x64),
      (alertasRemovidos[idRemover][0x1] = $(this).attr("style")),
      parseInt(_0xc9ba2b.html()) == 0x1
        ? (_0xc9ba2b.html("0").fadeOut(),
          _0x3855b9.find(".body li.vazio").show(),
          $("#messageAlert .icone").css("opacity", "0.5"))
        : _0xc9ba2b.html(parseInt(_0xc9ba2b.html()) - 0x1).fadeIn(),
      $(this).remove());
  });
}
function chavePrimaria(_0x102ed7, _0x5577b8) {
  _0x102ed7 = _0x102ed7.split("|");
  var _0x4d8a98 = 0x0;
  return (
    $.each(_0x102ed7, function (_0x1a22d0, _0x2e8df9) {
      var _0x499ebb = _0x2e8df9.split("-");
      _0x499ebb[0x0] == _0x5577b8 && (_0x4d8a98 = _0x499ebb[0x1]);
    }),
    _0x4d8a98
  );
}
function impressaoEtiquetas(
  _0x18be85,
  _0x5baf84,
  _0x3c92c3,
  _0x350c61,
  _0x88b6f7,
  _0x46310e,
  _0x3919b9,
) {
  _0x3919b9 = typeof _0x3919b9 == "undefined" ? "" : _0x3919b9;
  if ($(_0x18be85).closest(".popover-content").size() > 0x0)
    var _0x396cba = "popover",
      _0x9843f = $(_0x18be85).closest(".popover-content");
  else {
    var _0x396cba = "dialog",
      _0x9843f = $(_0x18be85).closest(".dialogConfirm");
    _0x9843f.css({ padding: "20px", "min-height": "0" });
  }
  if (typeof _0x46310e == "undefined" || !_0x46310e) {
    var _0x12bd7e = $("#appletImpressaoPreCarregado")
      .contents()
      .find("applet")[0x0]
      .listarImpressoras();
    if (_0x12bd7e == "")
      (_0x9843f.html(
        "Não existe nenhuma impressora configurada no seu sistema.",
      ),
        _0x9843f == "popover" &&
          setTimeout(function () {
            _0x9843f
              .closest(".popover")
              .find(".ui-icon-closethick")
              .trigger("click");
          }, 0x3e8));
    else {
      var _0x213bec = getCookie(_0x5baf84 + _0x350c61);
      _0x12bd7e = _0x12bd7e.split("\n");
      var _0x286a54 = '<select id="selecionarImpressora">';
      return (
        $.each(_0x12bd7e, function (_0x30c0f2, _0x426457) {
          _0x286a54 +=
            '<option value="' +
            _0x426457 +
            '" ' +
            (_0x426457 == _0x213bec ? " selected" : "") +
            ">" +
            _0x426457 +
            "</option>";
        }),
        (_0x286a54 += "</select>"),
        (_0x286a54 +=
          "&nbsp;&nbsp;<button onclick=\"impressaoEtiquetas(this, '" +
          _0x5baf84 +
          "', '" +
          _0x3c92c3 +
          "', '" +
          _0x350c61 +
          "', '" +
          _0x88b6f7 +
          "', $('#selecionarImpressora').val(), '" +
          _0x3919b9 +
          "'); return false;\";>Imprimir</button>"),
        $(_0x18be85).parent().css("display", "none"),
        $("<div></div>").html(_0x286a54).appendTo(_0x9843f),
        $("#selecionarImpressora").focus(),
        false
      );
    }
  } else
    (setCookie(_0x5baf84 + _0x350c61, _0x46310e, 0x0, true),
      $.ajax({
        url: _0x5baf84,
        dataType: "html",
        data:
          "ajax=true&acaoId=" +
          encodeURIComponent(_0x3c92c3) +
          "&imprimir=" +
          encodeURIComponent(_0x350c61) +
          "&quantidade=" +
          encodeURIComponent(_0x88b6f7) +
          "&impressora=" +
          encodeURIComponent(_0x46310e) +
          _0x3919b9,
        type: "post",
        success: function (_0xc0b2d2) {
          var _0x2cf221 = $("#appletImpressaoPreCarregado")
            .contents()
            .find("applet")[0x0]
            .imprimir(_0x46310e, _0xc0b2d2.replace("\n", ""));
          ((_0x2cf221 = _0x2cf221.replace("\n", "<br/>")),
            _0x9843f.html(_0x2cf221),
            setTimeout(
              function () {
                _0x9843f
                  .closest(".popover")
                  .find(".ui-icon-closethick")
                  .trigger("click");
              },
              _0x2cf221.match(/conclu.da/g) ? 0x5dc : 0x1388,
            ));
        },
      }));
}
function impressaoPreCarregarApplet() {
  $("#appletImpressaoPreCarregado").size() == 0x0
    ? $("<iframe></iframe>")
        .attr("id", "appletImpressaoPreCarregado")
        .css({
          width: "0",
          height: "0",
          position: "fixed",
          top: "-1000px",
          right: "-1000px",
        })
        .attr("src", "/hardness3/static/java/ImpByApplet.html")
        .appendTo("body")
    : alert("não carregar");
}
function mp2032PreCarregarApplet() {
  $("#appletmp2032PreCarregado").size() == 0x0 &&
    $("<iframe></iframe>")
      .attr("id", "appletmp2032PreCarregado")
      .css({
        width: "0",
        height: "0",
        position: "fixed",
        top: "-1000px",
        right: "-1000px",
      })
      .attr("src", "/hardness3/static/java/mp2032/Mp2032.html")
      .appendTo("body");
}
function Corrige_Numero(_0x4a7414) {
  return (
    (_0x4a7414 = _0x4a7414.replace(",", "")),
    (_0x4a7414 = _0x4a7414.replace(".", ",")),
    _0x4a7414
  );
}
function Corrige_Numero_Inverte(_0x48bdef) {
  return (
    (_0x48bdef = _0x48bdef.replace(".", "")),
    (_0x48bdef = _0x48bdef.replace(",", ".")),
    _0x48bdef
  );
}
function removerNotificacao(_0x38fabb, _0x477c3e, _0x4a8814) {
  (console.log("UniqueId " + _0x4a8814),
    $.getJSON(
      "/sistema/funcoes/removerNotificacao/" +
        _0x38fabb +
        "/?ajax=true&acaoId=" +
        encodeURIComponent(_0x477c3e) +
        "&Uniqid_Mensagem=" +
        encodeURIComponent(_0x4a8814) +
        "&callback=?",
      function (_0x93b4f1) {},
    ));
}
function acaoClickMensagemChat(_0x32ab47, _0x34ff9f, _0x46b693) {
  var _0x46b693 = typeof _0x46b693 == "undefined" ? false : _0x46b693,
    _0x6118c1 = $("#messageAlert .messages").find("li.alertid" + _0x32ab47);
  if (_0x6118c1.find(".overlay").size() > 0x0) returnfalse;
  (_0x6118c1.css("position", "relative"),
    $("<div></div>")
      .addClass("idTabelaOverlay")
      .css({
        position: "absolute",
        top: 0x0,
        left: 0x0,
        width: _0x6118c1.outerWidth(),
        height: _0x6118c1.outerHeight() - 0x1,
        "background-color": "black",
        opacity: "0.45",
      })
      .appendTo(_0x6118c1));
  var _0x27875a = $("<div></div>")
    .addClass("overlay")
    .css({
      position: "absolute",
      top: "0",
      left: "0",
      padding: "16px 0 0 121px",
    })
    .appendTo(_0x6118c1);
  (_0x34ff9f &&
    $("<button>Responder</button>")
      .click(function () {
        acaoClickLidoChat($(this).closest("li"), true);
      })
      .appendTo(_0x27875a),
    _0x46b693
      ? $("<button>Remover</button>")
          .css({ "margin-left": _0x34ff9f ? "5px" : "50px" })
          .click(function () {
            acaoClickRemoverAgenda($(this).closest("li"));
          })
          .appendTo(_0x27875a)
      : $("<button>Fechar</button>")
          .css({ "margin-left": _0x34ff9f ? "5px" : "50px" })
          .click(function () {
            acaoClickLidoChat($(this).closest("li"), false);
          })
          .appendTo(_0x27875a));
}
function acaoClickLidoChat(_0x3b3bd0, _0x1df432) {
  ((id = _0x3b3bd0.attr("class").split("-")),
    $.ajax({
      url: "/sistema/funcoes/chat/marcarLido/",
      dataType: "html",
      data: "ajax=true&T237_Id=" + encodeURIComponent(id[0x2]),
      type: "post",
      success: function (_0x1c58ae) {
        _0x3b3bd0.slideUp("fast", function () {
          alertRemover("chat-" + id[0x1] + "-" + id[0x2]);
          if (_0x1df432) {
            var _0x319bed = $("#messageAlert .boxenviarmensagem");
            (_0x319bed.find("select").val(id[0x1]),
              _0x319bed.find("textarea").val(""),
              !_0x319bed.is(":visible")
                ? $("#messageAlert .boxenviarmensagem").slideDown(
                    "normal",
                    function () {
                      _0x319bed.find("textarea").focus();
                    },
                  )
                : _0x319bed.find("textarea").focus());
          }
        });
      },
    }));
}
function acaoListarAgendadas() {
  $.ajax({
    url: "/sistema/funcoes/chat/verificarAgenda/",
    dataType: "html",
    data: "ajax=true",
    type: "post",
    success: function (_0x185b55) {
      return (
        $("#messageAlert .body .itemAgendado").remove(),
        $("#messageAlert .body ul li:eq(1)").after(_0x185b55),
        false
      );
    },
  });
}
function acaoClickRemoverAgenda(_0x1c88d4) {
  ((id = _0x1c88d4.attr("class").split("-")),
    $.ajax({
      url: "/sistema/funcoes/chat/removerMensagem/",
      dataType: "html",
      data: "ajax=true&T237_Id=" + encodeURIComponent(id[0x2]),
      type: "post",
      success: function (_0x521b57) {
        _0x1c88d4.slideUp("fast", function () {
          ($(
            "#messageAlert .messages li.alertidagenda" +
              id[0x1] +
              "-" +
              id[0x2],
          ).remove(),
            acaoListarAgendadas());
        });
      },
    }));
}
function pulsate() {}
function mostrarMais(_0x428e1f) {
  var _0x428e1f = $(_0x428e1f);
  (!_0x428e1f.data("titleDefinido") &&
    _0x428e1f.attr("title", _0x428e1f.text()),
    _0x428e1f.data("titleDefinido", 0x1));
}
function mostrarMenos(_0x25fdb0) {}
function strPad(_0x4a288f, _0x26943a, _0xec2b75) {
  return (
    (_0xec2b75 = _0xec2b75 || "0"),
    (_0x4a288f = _0x4a288f + ""),
    _0x4a288f.length >= _0x26943a
      ? _0x4a288f
      : new Array(_0x26943a - _0x4a288f.length + 0x1).join(_0xec2b75) +
        _0x4a288f
  );
}
function corrigirDirectWrite() {
  $(".formFormatoEditor:visible .formInput:visible").each(function () {
    var _0x184c6d = $(this).closest(".formEditor"),
      _0x3bb047 = $(this).position(),
      _0x5e8716 = _0x184c6d.position();
    if (_0x3bb047.left > 0x0) {
      var _0x421a72 = 0x10,
        _0x373b0a = (_0x3bb047.left + _0x5e8716.left) % _0x421a72;
      _0x373b0a <= 0x8
        ? _0x373b0a > 0x0 &&
          _0x184c6d.css({ left: _0x5e8716.left - _0x373b0a + "px" })
        : _0x184c6d.css({
            left: _0x5e8716.left - _0x373b0a + _0x421a72 + "px",
          });
    }
  });
}
function abrirEmailSuporte() {
  ($(".click").trigger("click"),
    $(".dialogConfirm").parent().css("z-index", "999999999999"),
    setTimeout(function () {
      html2canvas(document.body, {
        onrendered: function (_0x1019e3) {
          ((canvasImg = _0x1019e3.toDataURL("image/png")),
            $.getJSON(
              "/outros/registroSuporteCliente/registroSuporteCliente/abrirSuporteProprio/?ajax=true&callback=?",
              function (_0x241af2) {
                _0x241af2.suporteProprio
                  ? window.open(_0x241af2.url, "_blank")
                  : abrirJanela(
                      false,
                      divIdRootAberto(),
                      "",
                      menuId("RegSupCliente"),
                      "",
                      "Registro de Suporte",
                      "/outros/registroSuporteCliente/registroSuporteCliente/content/",
                      "&D024_Id=" + encodeURIComponent(g.D024_Id_Hardness),
                      [0x3de, 0x258],
                    );
              },
            ));
        },
      });
    }, 0x32));
}
function atualizarNotificacoes() {
  $.getJSON(
    "/sistema/funcoes/mensagens/atualizarNotificacoes/?ajax=true&callback=?",
    function (_0x5ade3e) {
      _0x5ade3e.code &&
        ($("[class*='alertidroot']")
          .not(".notifOculta")
          .each(function (_0x2bf3c4, _0x4fd5e2) {
            ($("#messageAlert .alerts").html(
              parseInt($("#messageAlert .alerts").html()) - 0x1,
            ),
              $(this).remove());
          }),
        setTimeout(function () {
          $.getJSON(
            "/sistema/funcoes/mensagens/montarNotificacoes/?ajax=true&callback=?",
            function (_0x458a76) {
              ((contadorAnterior = $("#messageAlert .alerts").text()),
                (contador = 0x0),
                $("#messageAlert .alerts").html(parseInt(0x0)),
                _0x458a76.code
                  ? eval(_0x458a76.data)
                  : console.log("Retorno ajax:" + _0x458a76.data),
                contadorAnterior != ""
                  ? contadorAnterior < contador
                    ? ($("#messageAlert .alerts").css({
                        "background-color": "red",
                        background:
                          "linear-gradient(center top , #FA3C45, #DC0D17) repeat scroll 0 0 transparent;",
                        "font-size": "14px",
                      }),
                      $("#messageAlert .alerts")
                        .html(parseInt(contador))
                        .fadeIn())
                    : ($("#messageAlert .alerts").css({
                        "background-color": "none",
                        "font-size": "14px",
                      }),
                      $("#messageAlert .alerts")
                        .html(parseInt(contador))
                        .fadeIn())
                  : ($("#messageAlert .alerts").css({
                      "background-color": "red",
                      background:
                        "linear-gradient(center top , #FA3C45, #DC0D17) repeat scroll 0 0 transparent;",
                      "font-size": "14px",
                    }),
                    $("#messageAlert .alerts")
                      .html(parseInt(contador))
                      .fadeIn()));
            },
          );
        }, 0x7d0));
    },
  );
}
function exibirOcultarNotificacao(_0x4a1f9c, _0x3de4b1, _0x8c8b5f) {
  var _0x13d3f3 = "",
    _0x9fcb42 = "";
  _0x8c8b5f == 0x0
    ? ((_0x13d3f3 = "Desabilitar notificação"),
      (_0x9fcb42 =
        "<b>ATENÇÃO</b><br><br> Você está desabilitando e não receberá mais essa notificação."))
    : ((_0x13d3f3 = "Habilitar notificação"),
      (_0x9fcb42 =
        "<b>ATENÇÃO</b><br><br> Você está habilitando e voltará a receber essa notificação."));
  _0x9fcb42 += "<br><br> Confirma?";
  var _0x1b9aae = {
    Sim: function () {
      ($.getJSON(
        "/sistema/funcoes/mensagens/exibirOcultarNotificacao/?ajax=true&notificacao=" +
          encodeURIComponent(_0x3de4b1) +
          "&ocultar=" +
          encodeURIComponent(_0x8c8b5f) +
          "&callback=?",
        function (_0x2aad11) {
          _0x2aad11.code &&
            (_0x8c8b5f == "0"
              ? (!$("#messageAlert .exibirOcultarNotificacoes").hasClass(
                  "exibindo",
                ) && $(_0x4a1f9c).closest("li").css("display", "none"),
                $(_0x4a1f9c).closest("li").addClass("notifOculta"),
                $(_0x4a1f9c)
                  .attr({
                    src: "/hardness3/static/img/check.png",
                    title: "Exibir notificação",
                  })
                  .attr(
                    "onclick",
                    "exibirOcultarNotificacao($(this), '" +
                      _0x3de4b1 +
                      "', '1'); event.stopPropagation();",
                  )
                  .css({
                    "-webkit-filter": "opacity(70%)",
                    filter: "opacity(70%)",
                  }),
                $("#messageAlert .alerts")
                  .html(parseInt($("#messageAlert .alerts").html()) - 0x1)
                  .fadeIn())
              : ($(_0x4a1f9c)
                  .attr({
                    src: "/hardness3/static/img/remove.png",
                    title: "Ocultar notificação",
                  })
                  .attr(
                    "onclick",
                    "exibirOcultarNotificacao($(this), '" +
                      _0x3de4b1 +
                      "', '0'); event.stopPropagation();",
                  )
                  .css({
                    "-webkit-filter": "opacity(70%)",
                    filter: "opacity(70%)",
                  }),
                $(_0x4a1f9c).closest("li").removeClass("notifOculta"),
                $("#messageAlert .alerts")
                  .html(parseInt($("#messageAlert .alerts").html()) + 0x1)
                  .fadeIn()));
        },
      ),
        $(this).dialog("destroy").remove());
    },
    Não: function () {
      $(this).dialog("destroy").remove();
    },
  };
  dialogConfirm(_0x13d3f3, _0x9fcb42, _0x1b9aae);
}
function salvarGridGlobal(_0x5d3ec6) {
  $("#" + _0x5d3ec6).showLoading();
  var _0x161fa6 = $(".content:visible:last");
  return (
    _0x161fa6.find(".ui-tabs-panel:visible:last").size() > 0x0 &&
      (_0x161fa6 = _0x161fa6.find(".ui-tabs-panel:visible:last")),
    $.getJSON(
      "/sistema/personalizacaoGrid/form_func-ajax/salvarGridGlobal/?ajax=true&grid=" +
        encodeURIComponent(_0x161fa6.data("href")) +
        "&callback=?",
      function (_0x198900) {
        $("#" + _0x5d3ec6).hideLoading();
        if (_0x198900.code) {
          var _0x316005 = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm(
            "Operação Ok",
            "As configurações do grid foram salvas com sucesso!",
            _0x316005,
          );
        } else {
          var _0x316005 = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x198900.data, _0x316005);
        }
      },
    ),
    false
  );
}
function alterarTamanhoFonte(_0x21b33c) {
  $.getJSON(
    "/sistema/funcoes/util/alterarTamanhoFonte/?ajax=true&tamanhoFonte=" +
      encodeURIComponent(_0x21b33c) +
      "&callback=?",
    function (_0x4e0a57) {
      if (_0x4e0a57.code)
        ($("body").showLoading(),
          setTimeout(function () {
            location.reload();
          }, 0x1f4));
      else {
        var _0x136219 = {
          Fechar: function () {
            $(this).dialog("destroy").remove();
          },
        };
        dialogConfirm("Erro", _0x4e0a57.data, _0x136219);
      }
    },
  );
}
function alterarTema(_0x466e88, _0x4cf682 = true) {
  $.getJSON(
    "/sistema/funcoes/util/alterarTema/?ajax=true&tema=" +
      encodeURIComponent(_0x466e88) +
      "&callback=?",
    function (_0x19f226) {
      if (_0x4cf682) {
        if (_0x19f226.code)
          ($("body").showLoading(),
            setTimeout(function () {
              location.reload();
            }, 0x1f4));
        else {
          var _0x15bb4f = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x19f226.data, _0x15bb4f);
        }
      }
    },
  );
}
function alterarLayout(_0x20ad04, _0x23cc7a = true) {
  $.getJSON(
    "/sistema/funcoes/util/alterarLayout/?ajax=true&layout=" +
      encodeURIComponent(_0x20ad04) +
      "&callback=?",
    function (_0x3157b6) {
      if (_0x23cc7a) {
        if (_0x3157b6.code)
          ($("body").showLoading(),
            setTimeout(function () {
              location.reload();
            }, 0x1f4));
        else {
          var _0x438fbd = {
            Fechar: function () {
              $(this).dialog("destroy").remove();
            },
          };
          dialogConfirm("Erro", _0x3157b6.data, _0x438fbd);
        }
      }
    },
  );
}
function mostrarOcultarCamposForm(_0x14d83b, _0x212ad8, _0x45d756, _0x4fe359) {
  ($(_0x212ad8 + " " + _0x45d756).change(function () {
    $.each(_0x14d83b, function (_0x2050d7, _0x4ff6a5) {
      var _0x363661 = parseInt($(_0x45d756).val());
      (jQuery.inArray(_0x363661, _0x4ff6a5[_0x4fe359]) !== -0x1
        ? ($(_0x212ad8 + " #" + _0x2050d7)
            .closest(".formEditor")
            .show(),
          $(_0x212ad8 + " #" + _0x2050d7).removeAttr("disabled"))
        : ($(_0x212ad8 + " #" + _0x2050d7).attr("disabled", "disabled"),
          $(_0x212ad8 + " #" + _0x2050d7)
            .closest(".formEditor")
            .hide()),
        $(_0x212ad8 + " #" + _0x2050d7).trigger("change"));
    });
  }),
    $(_0x212ad8 + " " + _0x45d756).trigger("change"));
}
function maximizarGrid(_0x379417) {
  var _0x159d2c = $("#" + _0x379417),
    _0x2d3016 = _0x159d2c.find(".gridTheadMaximizar:first"),
    _0x49ff66 = _0x159d2c.find("table.grid:first"),
    _0x22d7ed = _0x2d3016.closest(".ui-dialog").find(".content:first");
  (_0x22d7ed.find(".ui-tabs-panel").css("overflow", "visible"),
    _0x22d7ed.find(".content").css("overflow", "visible"),
    _0x22d7ed.css("position", "relative"),
    _0x22d7ed.find(".ui-tabs").css("position", "initial"),
    _0x2d3016.data("topOriginal", _0x2d3016.css("top")),
    _0x2d3016.css({ "z-index": "100003", top: "5px" }),
    _0x49ff66.parent().data("heightOriginal", _0x49ff66.parent().height()),
    _0x49ff66.parent().data("widthOriginal", _0x49ff66.parent().width()),
    _0x49ff66
      .parent()
      .data("marginTopOriginal", _0x49ff66.parent().css("margin-top")),
    _0x49ff66
      .parent()
      .css({
        "z-index": "100002",
        top: 0x7 + parseInt(_0x49ff66.parent().data("marginTopOriginal")),
        left: "0",
        right: "0",
        bottom: "0",
        position: "absolute",
        "margin-top": "",
        height: _0x22d7ed.height() - 0x7,
        overflow: "hidden",
      }),
    setTimeout(function () {
      _0x49ff66.parent().css({ overflow: "auto" });
    }, 0x64),
    $(
      '<div class="gridMaxOverlay" style="height: 100%; width: 100%; z-index: 100001; background: #EDE7E6 url(/hardness3/static/css/temas/padrao/images/ui-bg_dots-small_100_ede7de_2x2.png) 50% 50% repeat; position: absolute; top: 0;">',
    ).appendTo(_0x22d7ed),
    _0x159d2c.data("maximizado", true));
}
function minimizarGrid(_0x4cbd1f) {
  var _0x32db19 = $("#" + _0x4cbd1f),
    _0x2ccf5e = _0x32db19.find(".gridTheadMaximizar:first"),
    _0x56e5c7 = _0x32db19.find("table.grid:first"),
    _0x3bcda2 = _0x2ccf5e.closest(".ui-dialog").find(".content:first");
  (_0x3bcda2.find(".ui-tabs-panel").css("overflow", ""),
    _0x3bcda2.find(".content").css("overflow", ""),
    _0x3bcda2.css("position", ""),
    _0x3bcda2.find(".ui-tabs").css("position", ""),
    _0x2ccf5e.css({ "z-index": "", top: _0x2ccf5e.data("topOriginal") }),
    _0x56e5c7
      .parent()
      .css({
        "z-index": "",
        top: "",
        left: "",
        right: "",
        bottom: "",
        position: "",
        "margin-top": parseInt(_0x56e5c7.parent().data("marginTopOriginal")),
        height: _0x56e5c7.parent().data("heightOriginal"),
      }),
    _0x3bcda2.find(".gridMaxOverlay").remove(),
    _0x32db19.data("maximizado", false));
}
function removerLinhaGrid(_0xedac71, _0x5dc5a9, _0x5b8668) {
  var _0x523b80 = $("#" + _0xedac71),
    _0xcb1754;
  _0x5b8668
    ? (_0xcb1754 = _0x523b80.find(
        'tr[id*="' + _0x5dc5a9 + "-" + _0x5b8668 + '"]',
      ))
    : (_0xcb1754 = _0x523b80.find('tr[id*="' + _0x5dc5a9 + '"]'));
  if (_0xcb1754.hasClass("linhaSelecionada")) {
    var _0xf0449;
    ((_0xf0449 =
      _0xcb1754.prev().size() > 0x0 ? _0xcb1754.prev() : _0xcb1754.next()),
      _0xf0449.addClass("linhaSelecionada"));
    if (linhaSelecionadaModoCelula) {
      _0xf0449.addClass("linhaModoCelula");
      var _0x1350e5 = _0xf0449.find(
        "td:eq(" +
          _0xcb1754.find("td").index(_0xcb1754.find("td.colunaSelecionada")) +
          ")",
      );
      (_0x1350e5.data("background", _0x1350e5.css("background")),
        _0x1350e5
          .addClass("colunaSelecionada")
          .css("background", colunaSelecionadaBackgroud));
    }
  }
  _0xcb1754.remove();
  var _0x5607c8 = 0x0;
  _0x523b80.find(".grid tr[count]").each(function () {
    (_0x5607c8++,
      $(this).attr("count", _0x5607c8),
      $(this).find("td:first span.linhaCount").html(_0x5607c8));
  });
}
function eventosTeclado(_0x3636f9, _0x3beea9, _0x56412d) {
  var _0x56412d = typeof _0x56412d == "undefined" ? false : _0x56412d,
    _0x5db484 = $(":focus");
  if (
    !_0x56412d &&
    _0x5db484.size() > 0x0 &&
    (_0x5db484.is("textarea") ||
      _0x5db484.hasClass("nicEdit-main") ||
      _0x5db484.is("select") ||
      (_0x3beea9.target && $(_0x3beea9.target).is("select")) ||
      (_0x5db484.hasClass("campo_input_pesquisa") &&
        $(".ui-autocomplete:visible:first").size() != 0x0)) &&
    _0x3636f9 != "Tab" &&
    _0x3636f9 != "Shift_Tab"
  ) {
    if (
      _0x3beea9.target &&
      $(_0x3beea9.target).is("select") &&
      $(_0x3beea9.target).closest("tr.linhaSelecionada").size() > 0x0
    ) {
      if (_0x3636f9 == "Esc")
        return (
          _0x3beea9.stopPropagation(),
          _0x3beea9.preventDefault(),
          $(_0x3beea9.target).blur(),
          true
        );
      else {
        if (_0x3636f9 == "Left" || _0x3636f9 == "Right")
          $(_0x3beea9.target).blur();
        else returntrue;
      }
    } else returntrue;
  }
  var _0x15bee0 = false;
  !_0x15bee0 &&
    (_0x5db484.is(".ui-dialog")
      ? (_0x15bee0 = _0x5db484.find(".ui-dialog-content:first").attr("id"))
      : (_0x15bee0 = _0x5db484
          .closest(".ui-dialog")
          .find(".ui-dialog-content:first")
          .attr("id")));
  if (!_0x15bee0)
    for (var _0x2e546e in windows) {
      for (var _0x21d6c5 in windows[_0x2e546e]) {
        windows[_0x2e546e][_0x21d6c5].active && (_0x15bee0 = _0x21d6c5);
      }
    }
  var _0x54d641 = $("#topoMenu #barra li a.hover");
  if (_0x54d641.size() != 0x0) {
    if (_0x3636f9 == "Up" || _0x3636f9 == "Down") {
      var _0x214815 = _0x54d641.closest(".menuvertical"),
        _0x5dc5a2 = _0x214815.find("a"),
        _0x230373 = _0x5dc5a2.index(_0x54d641),
        _0xf0a7bd = _0x3636f9 == "Up" ? _0x230373 - 0x1 : _0x230373 + 0x1;
      if (_0xf0a7bd > -0x1 && _0xf0a7bd < _0x5dc5a2.size()) {
        var _0x168e39 = _0x5dc5a2.eq(_0xf0a7bd);
        (_0x54d641.trigger("mouseout"), _0x168e39.trigger("mouseover"));
      } else {
        $("#topoMenu #barra li a.hover").trigger("mouseout");
        if (_0x15bee0 && $(":focus").size() == 0x0) {
          var _0x594547 = $("#" + _0x15bee0);
          escolherFoco(_0x594547);
        }
      }
    } else {
      if (_0x3636f9 == "Left" || _0x3636f9 == "Right") {
        _0x54d641 = _0x54d641.closest(".menuvertical").find("a:first");
        var _0x214815 = _0x54d641.closest("#barra"),
          _0x5dc5a2 = _0x214815.find(".menuvertical > a"),
          _0x230373 = _0x5dc5a2.index(_0x54d641),
          _0xf0a7bd = _0x3636f9 == "Left" ? _0x230373 - 0x1 : _0x230373 + 0x1;
        if (_0xf0a7bd > -0x1 && _0xf0a7bd < _0x5dc5a2.size()) {
          var _0x168e39 = _0x5dc5a2.eq(_0xf0a7bd);
          (_0x54d641.trigger("mouseout"), _0x168e39.trigger("mouseover"));
        }
      } else {
        if (_0x3636f9 == "Esc") {
          $("#topoMenu #barra li a.hover").trigger("mouseout");
          if (_0x15bee0 && $(":focus").size() == 0x0) {
            var _0x594547 = $("#" + _0x15bee0);
            escolherFoco(_0x594547);
          }
        } else {
          if (_0x3636f9 == "Enter") {
            (_0x3beea9.preventDefault(), _0x3beea9.stopPropagation());
            _0x54d641.attr("onclick") && _0x54d641.trigger("click");
            $("#topoMenu #barra li a.hover").trigger("mouseout");
            if (_0x15bee0 && $(":focus").size() == 0x0) {
              var _0x594547 = $("#" + _0x15bee0);
              escolherFoco(_0x594547);
            }
          }
        }
      }
    }
    returntrue;
  }
  if (_0x15bee0) {
    var _0x594547 = $("#" + _0x15bee0);
    if ($(".popover").size() > 0x0) {
      $("body").trigger("mousedown");
      if (_0x3636f9 == "Esc" || _0x3636f9 == "Enter")
        return (_0x3beea9.preventDefault(), _0x3beea9.stopPropagation(), true);
    }
    var _0x1bf826 = [
        "Up",
        "Down",
        "Tab",
        "Shift_Tab",
        "Enter",
        "Right",
        "Left",
      ],
      _0x2c083c = false;
    _0x5db484.size() == 0x0 &&
      ((_0x2c083c = true),
      (_0x5db484 = _0x594547.find(
        ".grid:visible .linhaSelecionada:visible:first",
      )),
      (_0x1bf826 = ["Tab", "Shift_Tab", "Up", "Down"]));
    if (_0x1bf826.includes(_0x3636f9)) {
      var _0x52c74e = _0x594547.find(".grid:visible tr.linhaSelecionada:first");
      if (_0x2c083c && _0x3636f9 == "Up" && _0x52c74e.attr("count") == 0x1) {
        $gridMaxBtn = $(".gridTheadMaximizar:visible:last");
        if ($gridMaxBtn.find("span").hasClass("ui-icon-triangle-1-s"))
          returntrue;
        var _0x2cf4ac = _0x52c74e.find("td.colunaSelecionada");
        return (
          _0x2cf4ac.data(
            "backgroundColunaSelecionada",
            _0x2cf4ac.css("background"),
          ),
          _0x2cf4ac.css("background", _0x2cf4ac.data("background")),
          ($novoFoco = _0x594547.find(
            ".gridFiltroBotoes:visible:not([disabled]) button:visible:not([disabled]):first",
          )),
          $novoFoco.size() > 0x0 ? $novoFoco.focus() : escolherFoco(_0x594547),
          true
        );
      }
      _0x1cbf3a = parseInt(_0x52c74e.attr("count"));
      if (
        _0x2c083c &&
        _0x3636f9 == "Down" &&
        $("#" + _0x15bee0 + " .grid:visible:last")
          .find('tr[count="' + (_0x1cbf3a + 0x1) + '"]')
          .size() == 0x0
      ) {
        var _0x2cf4ac = _0x52c74e.find("td.colunaSelecionada");
        _0x2cf4ac != 0x0 &&
          (_0x2cf4ac.data(
            "backgroundColunaSelecionada",
            _0x2cf4ac.css("background"),
          ),
          _0x2cf4ac.css("background", _0x2cf4ac.data("background")));
      }
      if (_0x2c083c && (_0x3636f9 == "Shift_Tab" || _0x3636f9 == "Tab")) {
        var _0x2cf4ac = _0x52c74e.find("td.colunaSelecionada");
        (_0x2cf4ac.data(
          "backgroundColunaSelecionada",
          _0x2cf4ac.css("background"),
        ),
          _0x2cf4ac.css("background", _0x2cf4ac.data("background")));
      }
      if (_0x5db484.size() > 0x0) {
        var _0x48fd4d = _0x5db484.closest(
          ".buttonsBottomTop:visible:not([disabled]), " +
            ".ui-tabs-nav:visible:not([disabled]), " +
            ".gridFiltro:visible:not([disabled]), " +
            ".gridFiltroBotoes:visible:not([disabled]), " +
            ".formFormulario:visible:not([disabled]), " +
            ".grid:visible:last, " +
            ".buttonsBottom:visible:not([disabled]), " +
            ".ui-dialog-buttonpane",
        );
        if (
          _0x48fd4d.hasClass("buttonsBottomTop") ||
          _0x48fd4d.hasClass("ui-tabs-nav") ||
          _0x48fd4d.hasClass("gridFiltro") ||
          _0x48fd4d.hasClass("gridFiltroBotoes") ||
          _0x48fd4d.hasClass("formFormulario") ||
          _0x48fd4d.hasClass("buttonsBottom") ||
          _0x48fd4d.hasClass("ui-dialog-buttonpane") ||
          (_0x48fd4d.hasClass("grid") &&
            !_0x5db484.hasClass("input-editar-campo") &&
            !_0x5db484.hasClass("formInput") &&
            !_0x5db484.hasClass("input-editar-campo-rapido"))
        ) {
          _0x3636f9 != "Enter" &&
            _0x3636f9 != "Right" &&
            _0x3636f9 != "Left" &&
            !_0x48fd4d.hasClass("formFormulario") &&
            (_0x3beea9.stopPropagation(), _0x3beea9.preventDefault());
          if (
            (!_0x2c083c || _0x56412d) &&
            (_0x3636f9 == "Up" || _0x3636f9 == "Down")
          ) {
            if (_0x48fd4d.hasClass("ui-dialog-buttonpane")) {
              if (
                _0x48fd4d.closest(".ui-dialog").find(".dialogConfirm").size() >
                0x0
              )
                returntrue;
            }
            var _0x5dc5a2 = _0x594547.find(
                ".buttonsBottomTop:visible:not([disabled]), " +
                  ".ui-tabs-nav:visible:not([disabled]), " +
                  ".gridFiltro:visible:not([disabled]), " +
                  ".gridFiltroBotoes:visible:not([disabled]), " +
                  ".formFormulario:visible:not([disabled]), " +
                  ".grid:visible:last, " +
                  ".buttonsBottom:visible:not([disabled])",
              ),
              _0x325e25 = null;
            for (
              var _0x3f697e = 0x0;
              _0x3f697e < _0x5dc5a2.size();
              _0x3f697e++
            ) {
              if (_0x5dc5a2.eq(_0x3f697e).hasClass("formFormulario"))
                _0x325e25 = _0x3f697e;
              else {
                if (
                  _0x5dc5a2.eq(_0x3f697e).hasClass("ui-tabs-nav") &&
                  _0x325e25 != null
                ) {
                  var _0x5ae328 = _0x5dc5a2[_0x325e25];
                  ((_0x5dc5a2[_0x325e25] = _0x5dc5a2[_0x3f697e]),
                    (_0x5dc5a2[_0x3f697e] = _0x5ae328));
                }
              }
            }
            var _0x230373 = _0x5dc5a2.index(_0x48fd4d),
              _0xf0a7bd = _0x3636f9 == "Up" ? _0x230373 - 0x1 : _0x230373 + 0x1,
              _0x168e39 = _0x5dc5a2.eq(_0xf0a7bd),
              _0x4bfe86 = 0x0;
            while (
              _0x4bfe86 < 0xa &&
              _0xf0a7bd > -0x1 &&
              _0xf0a7bd < _0x5dc5a2.size() &&
              (((_0x168e39.hasClass("buttonsBottomTop") ||
                _0x168e39.hasClass("buttonsBottom")) &&
                _0x168e39.find("button:visible:not([disabled]):first").size() ==
                  0x0) ||
                (_0x168e39.hasClass("grid") &&
                  _0x168e39.find("tbody tr").size() == 0x0) ||
                (_0x168e39.hasClass("formFormulario") &&
                  _0x168e39
                    .find(
                      "input:visible:not([disabled]):not([type=hidden]):first, textarea:visible:not([disabled]):not([type=hidden]):first",
                    )
                    .size() == 0x0) ||
                (_0x168e39.hasClass("gridFiltro") &&
                  _0x168e39
                    .find(
                      "input:visible:not([disabled]):not([type=hidden]):first",
                    )
                    .size() == 0x0))
            ) {
              (_0x4bfe86++,
                (_0xf0a7bd =
                  _0x3636f9 == "Up" ? _0xf0a7bd - 0x1 : _0xf0a7bd + 0x1),
                (_0x168e39 = _0x5dc5a2.eq(_0xf0a7bd)));
            }
            if (_0x5db484.hasClass("campo_input_data"))
              _0x5db484.datepicker("hide");
            else
              _0x5db484.hasClass("campo_input_datahora") &&
                _0x5db484.datetimepicker("hide");
            _0x5db484.blur();
            if (_0xf0a7bd > -0x1 && _0xf0a7bd < _0x5dc5a2.size()) {
              if (_0x168e39.hasClass("buttonsBottomTop"))
                _0x168e39.find("button:visible:not([disabled]):first").focus();
              else {
                if (_0x168e39.hasClass("ui-tabs-nav"))
                  _0x168e39
                    .find("li.ui-tabs-selected:visible:not([disabled])")
                    .focus();
                else {
                  if (_0x168e39.hasClass("gridFiltro"))
                    _0x168e39
                      .find("input:visible:not([disabled]):first")
                      .first()
                      .focus();
                  else {
                    if (_0x168e39.hasClass("gridFiltroBotoes"))
                      _0x168e39
                        .find("button:visible:not([disabled]):first")
                        .first()
                        .focus();
                    else {
                      if (_0x168e39.hasClass("formFormulario"))
                        _0x168e39
                          .find(
                            "input:visible:not([disabled]):not([type=hidden]):first, textarea:visible:not([disabled]):not([type=hidden]):first",
                          )
                          .first()
                          .focus();
                      else
                        _0x168e39.hasClass("buttonsBottom") &&
                          _0x168e39
                            .find("button:visible:not([disabled]):first")
                            .focus();
                    }
                  }
                }
              }
              if (_0x168e39.hasClass("grid"))
                _0x3636f9 != "Esc" &&
                  ((_0x2cf4ac = _0x52c74e.find("td.colunaSelecionada")),
                  _0x2cf4ac.css(
                    "background",
                    _0x2cf4ac.data("backgroundColunaSelecionada"),
                  ));
              else returntrue;
            }
            if (_0x3636f9 == "Up" && _0xf0a7bd <= -0x1)
              return (
                $("#topoMenu #barra li a.hover").size() == 0x0 &&
                  $("#topoMenu #barra .menuvertical:eq(0) a:first").trigger(
                    "mouseover",
                  ),
                true
              );
            else {
              if (_0x52c74e.size() == 0x0) {
              } else returntrue;
            }
          } else {
            if (
              _0x3636f9 == "Tab" ||
              _0x3636f9 == "Shift_Tab" ||
              _0x3636f9 == "Right" ||
              _0x3636f9 == "Left"
            ) {
              if (_0x48fd4d.hasClass("buttonsBottomTop"))
                var _0x3fcd39 = _0x48fd4d.find(
                  "button:visible:not([disabled])",
                );
              else {
                if (_0x48fd4d.hasClass("ui-tabs-nav"))
                  var _0x3fcd39 = _0x48fd4d.find("li:visible:not([disabled])");
                else {
                  if (_0x48fd4d.hasClass("gridFiltro")) {
                    if (_0x3636f9 == "Right" || _0x3636f9 == "Left") returntrue;
                    var _0x3fcd39 = _0x48fd4d.find(
                      "input:visible:not([disabled])",
                    );
                  } else {
                    if (_0x48fd4d.hasClass("gridFiltroBotoes"))
                      var _0x3fcd39 = _0x48fd4d.find(
                        "button:visible:not([disabled])",
                      );
                    else {
                      if (_0x48fd4d.hasClass("formFormulario")) returntrue;
                      else {
                        if (_0x48fd4d.hasClass("buttonsBottom"))
                          var _0x3fcd39 = _0x48fd4d.find(
                            "button:visible:not([disabled])",
                          );
                        else {
                          if (_0x48fd4d.hasClass("grid"))
                            return (
                              _0x3636f9 == "Tab"
                                ? eventosTeclado("Down", _0x3beea9, true)
                                : eventosTeclado("Up", _0x3beea9, true),
                              true
                            );
                          else {
                            if (_0x48fd4d.hasClass("ui-dialog-buttonpane"))
                              var _0x3fcd39 = _0x48fd4d.find(
                                "button:visible:not([disabled])",
                              );
                          }
                        }
                      }
                    }
                  }
                }
              }
              var _0x164d46 = _0x3fcd39.index(_0x5db484),
                _0x4efa51 =
                  _0x3636f9 == "Tab" || _0x3636f9 == "Right"
                    ? _0x164d46 + 0x1
                    : _0x164d46 - 0x1;
              if (_0x4efa51 == -0x1)
                !_0x48fd4d.hasClass("ui-dialog-buttonpane") &&
                  eventosTeclado("Up", _0x3beea9, true);
              else {
                if (_0x4efa51 == _0x3fcd39.size())
                  !_0x48fd4d.hasClass("ui-dialog-buttonpane") &&
                    eventosTeclado("Down", _0x3beea9, true);
                else {
                  var _0x177b93 = _0x3fcd39.eq(_0x4efa51);
                  _0x177b93.focus();
                }
              }
              returntrue;
            } else {
              if (_0x3636f9 == "Enter") {
                if (_0x48fd4d.hasClass("ui-tabs-nav"))
                  return (
                    _0x5db484
                      .find("a:visible:not([disabled]):first")
                      .trigger("click"),
                    _0x3beea9.stopPropagation(),
                    _0x3beea9.preventDefault(),
                    true
                  );
              }
            }
          }
        } else {
          if (_0x52c74e.size() == 0x0)
            return (escolherFoco(_0x594547, true), true);
        }
      } else {
        if (_0x5db484.size() == 0x0 && _0x52c74e.size() == 0x0)
          return (escolherFoco(_0x594547, true), true);
      }
    }
    if (
      (_0x3636f9 == "Right" ||
        _0x3636f9 == "Left" ||
        _0x3636f9 == "Home" ||
        _0x3636f9 == "End") &&
      (_0x5db484.hasClass("input-editar-campo") ||
        _0x5db484.hasClass("formInput") ||
        _0x5db484.hasClass("input-editar-campo-rapido"))
    )
      returntrue;
    var _0x2fa5ac = $("#" + _0x15bee0 + " .grid:visible:last");
    if (
      _0x5db484.size() != 0x0 &&
      _0x5db484.closest(".grid:visible").size() != 0x0
    )
      _0x2fa5ac = _0x5db484.closest(".grid:visible");
    else
      $("#" + _0x15bee0 + " .grid:visible tr.linhaSelecionada:first").size() !=
        0x0 && (_0x2fa5ac = _0x52c74e.closest(".grid:visible"));
    if (
      _0x2fa5ac
        .closest(".ui-dialog")
        .find(".ui-dialog-content:first")
        .attr("id") !=
      $(".ui-dialog:visible:last .ui-dialog-content:visible:first").attr("id")
    )
      returntrue;
    if (_0x2fa5ac.size() > 0x0) {
      var _0x52c74e = _0x2fa5ac.find(".linhaSelecionada");
      !currentFocus &&
        _0x3636f9 != "Enter" &&
        _0x3636f9 != "Esc" &&
        (_0x3beea9.stopPropagation(), _0x3beea9.preventDefault());
      _0x3636f9 != "Esc" &&
        ((_0x2cf4ac = _0x52c74e.find("td.colunaSelecionada")),
        _0x2cf4ac.data("backgroundColunaSelecionada") &&
          (_0x2cf4ac.css(
            "background",
            _0x2cf4ac.data("backgroundColunaSelecionada"),
          ),
          _0x2cf4ac.data("backgroundColunaSelecionada", 0x0)));
      if (
        _0x3636f9 == "Esc" &&
        _0x3beea9.target &&
        $(_0x3beea9.target).hasClass("input-editar-campo")
      )
        return (
          _0x3beea9.stopPropagation(),
          _0x3beea9.preventDefault(),
          $(currentFocus).val($(currentFocus).data("oldValue")),
          $(currentFocus).blur(),
          false
        );
      if (_0x3636f9 == "Esc" && _0x52c74e.size() > 0x0) {
        var _0x5b17de = $.Event("keydown", { keyCode: 0x1b });
        _0x594547.trigger(_0x5b17de);
      }
      var _0x1cbf3a, _0x4a6add;
      if (_0x52c74e.size() > 0x0)
        ((_0x1cbf3a = parseInt(_0x52c74e.attr("count"))),
          linhaSelecionadaModoCelula &&
            (_0x4a6add = _0x52c74e
              .find("td")
              .index(_0x52c74e.find("td.colunaSelecionada"))));
      else
        _0x3636f9 == "Down"
          ? ((_0x1cbf3a = 0x0), (_0x4a6add = 0x0))
          : ((_0x1cbf3a = 0x1), (_0x4a6add = 0x0));
      var _0x11d490 = false,
        _0x59faa1 = false;
      if (_0x3636f9 == "Page_up") {
        if (_0x1cbf3a == 0x1)
          return (
            _0x594547.find(".gridFiltro input:visible:first").focus(),
            _0x2fa5ac
              .find("tr.linhaSelecionada")
              .removeClass("linhaSelecionada"),
            false
          );
        var _0x11d490 = _0x2fa5ac.find('tr[count="' + (_0x1cbf3a - 0xa) + '"]');
        if (_0x11d490.size() == 0x0) {
          _0x1cbf3a = 0x2;
          var _0x11d490 = _0x2fa5ac.find(
            'tr[count="' + (_0x1cbf3a - 0x1) + '"]',
          );
        }
        linhaSelecionadaModoCelula &&
          (_0x59faa1 = _0x11d490.find("td:eq(" + _0x4a6add + ")"));
      } else {
        if (_0x3636f9 == "Page_down") {
          currentFocus && $(currentFocus).blur();
          var _0x11d490 = _0x2fa5ac.find(
            'tr[count="' + (_0x1cbf3a + 0xa) + '"]',
          );
          _0x11d490.size() == 0x0 &&
            ((_0x11d490 = _0x2fa5ac.find("tr:not('.trLoading'):last")),
            _0x2fa5ac.find("tr.trLoading").size() > 0x0 &&
              _0x2fa5ac.parent().scrollTop(_0x2fa5ac.height() + 0x64));
          if (_0x11d490.size() == 0x0) returnfalse;
          linhaSelecionadaModoCelula &&
            (_0x59faa1 = _0x11d490.find("td:eq(" + _0x4a6add + ")"));
        } else {
          if (_0x3636f9 == "Up") {
            _0x5db484.blur();
            var _0x11d490 = _0x2fa5ac.find(
              'tr[count="' + (_0x1cbf3a - 0x1) + '"]',
            );
            if (_0x1cbf3a == 0x1) returnfalse;
            linhaSelecionadaModoCelula &&
              (_0x59faa1 = _0x11d490.find("td:eq(" + _0x4a6add + ")"));
          } else {
            if (_0x3636f9 == "Down") {
              _0x5db484.blur();
              var _0x11d490 = _0x2fa5ac.find(
                'tr[count="' + (_0x1cbf3a + 0x1) + '"]',
              );
              if (_0x11d490.size() == 0x0) {
                if (_0x2fa5ac.find("tr.trLoading").size() > 0x0)
                  return (
                    _0x2fa5ac.parent().scrollTop(_0x2fa5ac.height() + 0x64),
                    false
                  );
                return (eventosTeclado("Down", _0x3beea9, true), true);
              }
              linhaSelecionadaModoCelula &&
                (_0x59faa1 = _0x11d490.find("td:eq(" + _0x4a6add + ")"));
            } else {
              if (_0x3636f9 == "Enter") {
                if (
                  _0x5db484.hasClass("input-editar-campo") &&
                  !_0x5db484.hasClass("input-editar-campo-rapido")
                )
                  return (
                    _0x5db484.closest("tr").next().size() > 0x0 &&
                      eventosTeclado("Down", _0x3beea9),
                    true
                  );
                if (currentFocus && !_0x5db484.hasClass("linhaSelecionada"))
                  returntrue;
                if (_0x5db484.hasClass("ui-button"))
                  return (_0x5db484.removeClass("ui-state-active"), true);
                return (
                  setTimeout(function () {
                    if (!linhaSelecionadaModoCelula)
                      _0x2fa5ac
                        .find("tr.linhaSelecionada:visible:first")
                        .trigger("click");
                    else {
                      var _0xd0f51c = _0x2fa5ac.find(
                          "tr.linhaSelecionada:first td.colunaSelecionada:first",
                        ),
                        _0x18c101 = _0xd0f51c.find('input[type="checkbox"]');
                      if (_0xd0f51c.find("*[onclick]").size() > 0x0)
                        (_0xd0f51c.find("*[onclick]")[0x0].onclick(),
                          _0x18c101.size() > 0x0 &&
                            _0x18c101.prop(
                              "checked",
                              _0x18c101.prop("checked") ? false : true,
                            ));
                      else {
                        if (_0xd0f51c.attr("onclick"))
                          _0xd0f51c.trigger("click");
                        else
                          _0x18c101.size() > 0x0
                            ? _0x18c101.prop(
                                "checked",
                                _0x18c101.prop("checked") ? false : true,
                              )
                            : _0x2fa5ac
                                .find("tr.linhaSelecionada:visible:first")
                                .trigger("click");
                      }
                    }
                  }, 0xa),
                  false
                );
              } else {
                if (_0x3636f9 == "Home") {
                  if (!linhaSelecionadaModoCelula) {
                    var _0x201cb8 = _0x594547
                        .find(".divToScroll:visible")
                        .scrollLeft(),
                      _0xc5a7e4 = _0x594547
                        .find(".divToScroll:visible")
                        .width();
                    _0x594547
                      .find(".divToScroll:visible")
                      .scrollLeft(_0x201cb8 - _0xc5a7e4 * 0.8);
                  } else {
                    _0x5db484.blur();
                    if (_0x52c74e.size() > 0x0) {
                      var _0x11d490 = _0x52c74e;
                      ((_0x59faa1 = _0x11d490.find(
                        "td:eq(" + (_0x4a6add - 0x4) + ")",
                      )),
                        _0x59faa1.size() == 0x0 &&
                          (_0x59faa1 = _0x11d490.find("td:first")));
                    }
                  }
                } else {
                  if (_0x3636f9 == "End") {
                    if (!linhaSelecionadaModoCelula) {
                      var _0x201cb8 = _0x594547
                          .find(".divToScroll:visible")
                          .scrollLeft(),
                        _0xc5a7e4 = _0x594547
                          .find(".divToScroll:visible")
                          .width();
                      _0x594547
                        .find(".divToScroll:visible")
                        .scrollLeft(_0x201cb8 + _0xc5a7e4 * 0.8);
                    } else {
                      _0x5db484.blur();
                      if (_0x52c74e.size() > 0x0) {
                        var _0x11d490 = _0x52c74e;
                        ((_0x59faa1 = _0x11d490.find(
                          "td:eq(" + (_0x4a6add + 0x4) + ")",
                        )),
                          _0x59faa1.size() == 0x0 &&
                            (_0x59faa1 = _0x11d490.find("td:last")));
                      }
                    }
                  } else {
                    if (_0x3636f9 == "Alt_E") {
                      var _0x52c74e = _0x2fa5ac.find(".linhaSelecionada");
                      _0x52c74e.size() > 0x0 &&
                        _0x52c74e.find(".campoEditavel:first").trigger("click");
                    } else {
                      if (_0x3636f9 == "Alt_D") {
                        var _0x52c74e = _0x2fa5ac.find(".linhaSelecionada");
                        _0x52c74e.size() > 0x0 &&
                          _0x52c74e
                            .find(".ui-icon-circle-close:first")
                            .parent()
                            .trigger("click");
                      } else {
                        if (_0x3636f9 == "Tab" || _0x3636f9 == "Shift_Tab") {
                          ultimoCampoTab = [];
                          if (
                            ($(currentFocus).hasClass("input-editar-campo") ||
                              $(currentFocus).hasClass("formInput")) &&
                            !$(currentFocus).hasClass(
                              "input-editar-campo-rapido",
                            )
                          ) {
                            if (_0x3636f9 == "Tab")
                              var _0x34d172 = $(currentFocus)
                                .closest("td")
                                .nextAll(".campoEditavel:first");
                            else
                              var _0x34d172 = $(currentFocus)
                                .closest("td")
                                .prevAll(".campoEditavel:first");
                            if (_0x34d172.size() == 0x0) {
                              if (_0x3636f9 == "Tab")
                                var _0x34d172 = $(currentFocus)
                                  .closest("tr")
                                  .next()
                                  .find(".campoEditavel:first");
                              else
                                var _0x34d172 = $(currentFocus)
                                  .closest("tr")
                                  .prev()
                                  .find(".campoEditavel:last");
                              _0x34d172.size() > 0x0 &&
                                (_0x3636f9 == "Tab"
                                  ? eventosTeclado("Down", _0x3beea9)
                                  : eventosTeclado("Up", _0x3beea9),
                                _0x3beea9.stopPropagation(),
                                _0x3beea9.preventDefault());
                            } else {
                              if (linhaSelecionadaModoCelula) {
                                var _0x11d490 = _0x52c74e;
                                _0x59faa1 = _0x34d172;
                              }
                              ((ultimoCampoTab = [
                                _0x34d172.closest("tr").attr("id"),
                                _0x34d172
                                  .closest("tr")
                                  .find("td")
                                  .index(_0x34d172),
                              ]),
                                _0x34d172.trigger("click"),
                                _0x3beea9.stopPropagation(),
                                _0x3beea9.preventDefault());
                            }
                          }
                        } else {
                          if (_0x3636f9 == "Left") {
                            _0x5db484.blur();
                            if (
                              linhaSelecionadaModoCelula &&
                              _0x52c74e.size() > 0x0
                            ) {
                              var _0x11d490 = _0x52c74e;
                              _0x59faa1 = _0x11d490.find(
                                "td:eq(" + (_0x4a6add - 0x1) + ")",
                              );
                            }
                          } else {
                            if (_0x3636f9 == "Right") {
                              _0x5db484.blur();
                              if (
                                linhaSelecionadaModoCelula &&
                                _0x52c74e.size() > 0x0
                              ) {
                                var _0x11d490 = _0x52c74e;
                                _0x59faa1 = _0x11d490.find(
                                  "td:eq(" + (_0x4a6add + 0x1) + ")",
                                );
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
      if (_0x11d490) {
        if (linhaSelecionadaModoCelula && _0x59faa1.size() == 0x0) returntrue;
        _0x52c74e
          .removeClass("linhaSelecionada")
          .removeClass("linhaModoCelula");
        var _0x2cf4ac = _0x52c74e.find("td.colunaSelecionada");
        (_0x2cf4ac.removeClass("colunaSelecionada"),
          _0x2cf4ac.css("background", _0x2cf4ac.data("background")),
          _0x11d490.addClass("linhaSelecionada"));
        linhaSelecionadaModoCelula &&
          (_0x11d490.addClass("linhaModoCelula"),
          _0x59faa1.data("background", _0x59faa1.css("background")),
          _0x59faa1.addClass("colunaSelecionada"),
          _0x59faa1.css("background", colunaSelecionadaBackgroud));
        if (_0x11d490.hasClass("color1")) {
        }
        var _0xef66f2 = 0x0,
          _0x3a3062 = 0x0;
        if (
          _0x594547
            .find(".divToScroll:visible:last tr.linhaSelecionada")
            .size() > 0x0
        )
          var _0xef66f2 = _0x594547
              .find(".divToScroll:visible:last tr.linhaSelecionada")
              .offset().top,
            _0x3a3062 = _0x594547
              .find(".divToScroll:visible:last tr.linhaSelecionada")
              .outerHeight();
        var _0x494d33 = _0x594547
            .find(".divToScroll:visible:last")
            .offset().top,
          _0x35a20d = _0x594547.find(".divToScroll:visible:last").outerHeight(),
          _0x5bade1 = _0x594547.find(".divToScroll:visible:last").scrollTop();
        if (_0xef66f2 - _0x494d33 + _0x3a3062 > _0x35a20d - 0x14)
          _0x594547
            .find(".divToScroll:visible:last")
            .scrollTop(
              0x1 +
                _0x5bade1 +
                _0xef66f2 -
                _0x494d33 +
                _0x3a3062 -
                (_0x35a20d - 0x14),
            );
        else
          _0xef66f2 - _0x494d33 < 0x0 &&
            _0x594547
              .find(".divToScroll:visible:last")
              .scrollTop(0x1 + _0x5bade1 + (_0xef66f2 - _0x494d33) - 0xa);
        if (linhaSelecionadaModoCelula) {
          var _0x381c77 = _0x59faa1.offset().left,
            _0x201cb8 = _0x594547
              .find(".divToScroll:visible:last")
              .offset().left,
            _0xc5a7e4 = _0x594547.find(".divToScroll:visible:last").width(),
            _0x5437b7 = _0x59faa1.outerWidth(),
            _0x44d83c = _0x594547
              .find(".divToScroll:visible:last")
              .scrollLeft();
          if (_0x381c77 - _0x201cb8 + _0x5437b7 > _0xc5a7e4 - 0x14)
            _0x594547
              .find(".divToScroll:visible:last")
              .scrollLeft(
                _0x44d83c +
                  _0x381c77 -
                  _0x201cb8 +
                  _0x5437b7 -
                  (_0xc5a7e4 - 0x14),
              );
          else
            _0x381c77 - _0x201cb8 < 0x0 &&
              _0x594547
                .find(".divToScroll:visible:last")
                .scrollLeft(_0x44d83c + (_0x381c77 - _0x201cb8) - 0xa);
        }
        returnfalse;
      }
      returntrue;
    }
  } else {
    if ($("#topoMenu #barra li a.hover").size() == 0x0) {
      if (_0x3636f9 == "Up" || _0x3636f9 == "Down" || _0x3636f9 == "Left")
        $("#topoMenu #barra .menuvertical:eq(0) a:first").trigger("mouseover");
      else
        _0x3636f9 == "Right" &&
          $("#topoMenu #barra .menuvertical:eq(1) a:first").trigger(
            "mouseover",
          );
    }
    returntrue;
  }
}
function escolherFoco(_0x3a6dcf, _0x37b637) {
  var _0x37b637 = typeof _0x37b637 == "undefined" ? false : _0x37b637,
    _0x3f853b;
  if (_0x37b637) {
    _0x3f853b = _0x3a6dcf.find(".grid:visible:last tbody tr:visible:first");
    if (_0x3f853b.size() > 0x0) {
      _0x3f853b.addClass("linhaSelecionada");
      if (linhaSelecionadaModoCelula) {
        _0x3f853b.addClass("linhaModoCelula");
        var _0x5296bd = _0x3f853b.find("td:visible:first");
        (_0x5296bd.data("background", _0x5296bd.css("background")),
          _0x5296bd.addClass("colunaSelecionada"),
          _0x5296bd.css("background", colunaSelecionadaBackgroud));
      }
      returntrue;
    }
  }
  _0x3f853b = _0x3a6dcf.find(
    ".gridFiltro:visible:not([disabled]) input:visible:not([disabled]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".formFormulario:visible:not([disabled]) input:visible:not([disabled]):not([type=hidden]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".formFormulario:visible:not([disabled]) textarea:visible:not([disabled]):not([type=hidden]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".buttonsBottomTop:visible:not([disabled]) button:visible:not([disabled]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".ui-tabs-nav:visible:not([disabled]):first li.ui-tabs-selected:visible:not([disabled]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".buttonsBottom:visible:not([disabled]) button:visible:not([disabled]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
  _0x3f853b = _0x3a6dcf.find(
    ".ui-dialog-buttonpane button:visible:not([disabled]):first",
  );
  if (_0x3f853b.size() > 0x0) return (_0x3f853b.focus(), true);
}
function abrirMenuPorNome(_0x2ba516) {
  return function (_0x3fb227) {
    if (_0x3fb227.code == "F5") return;
    var _0x26b409 = $("#topoMenu #barra li a.hover");
    (_0x26b409.size() != 0x0 && _0x26b409.trigger("mouseout"),
      $(
        '#topoMenu #barra .menuvertical a:contains("' + _0x2ba516 + '")',
      ).trigger("mouseover"));
  };
}
function selecionarCelula(_0x307273) {
  var _0x5bfe18 = _0x307273.closest("table.grid");
  if (_0x307273.size() > 0x0 && _0x5bfe18.size() > 0x0) {
    var _0x42ffdd = _0x307273.closest("tr");
    (_0x5bfe18
      .find("tr.linhaSelecionada")
      .removeClass("linhaSelecionada")
      .removeClass("linhaModoCelula"),
      _0x42ffdd.addClass("linhaSelecionada"));
    if (linhaSelecionadaModoCelula) {
      _0x42ffdd.addClass("linhaModoCelula");
      var _0x588b90 = _0x5bfe18.find("td.colunaSelecionada:first");
      (_0x588b90.size() > 0x0 &&
        (_0x588b90.removeClass("colunaSelecionada"),
        _0x588b90.css("background", _0x588b90.data("background"))),
        _0x307273.data("background", _0x307273.css("background")),
        _0x307273.addClass("colunaSelecionada"),
        _0x307273.css("background", colunaSelecionadaBackgroud));
    }
  }
}
function processaEtapas(
  _0x2a449f,
  _0x41e264,
  _0x5299a1,
  _0x35645c,
  _0x410a82,
  _0x505bc5,
  _0x23e692,
  _0xf56961,
  _0x38884a,
) {
  var _0x32ab00 = _0x5299a1.split(";"),
    _0x4a07f2 = _0x410a82 + 0x1,
    _0x38f458 = _0x32ab00[_0x410a82].split("|"),
    _0x22cc82 = _0x38884a === true;
  if (_0x32ab00.length > _0x410a82) {
    if (_0x410a82 == 0x0) {
      var _0x3051c2 = function () {
        ((idProgressBar = dialogAlert(
          _0x2a449f,
          "<span id='progressBar'><br />Processando: processo: " +
            _0x4a07f2 +
            "/" +
            _0x32ab00.length +
            " = 0%</span><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' />",
        )),
          _0x38f458[0x1] == "false"
            ? (processaEtapasProgressBar(
                _0x2a449f,
                _0x4a07f2,
                _0x32ab00.length,
                0x0,
                0x0,
              ),
              processaEtapaUnica(
                idProgressBar,
                _0x2a449f,
                _0x41e264,
                _0x38f458[0x0],
                _0x35645c,
                _0x410a82,
                _0x5299a1,
                _0x505bc5,
                _0x23e692,
                _0xf56961,
                _0x22cc82,
              ))
            : processaEtapasLoop(
                idProgressBar,
                0x1,
                _0x2a449f,
                _0x41e264,
                _0x32ab00[_0x410a82],
                _0x35645c,
                _0x410a82,
                _0x5299a1,
                _0x505bc5,
                _0x23e692,
                _0xf56961,
                _0x22cc82,
              ));
      };
      if (_0x22cc82) _0x3051c2();
      else {
        var _0x5869f3 = {
          Sim: function () {
            ($(this).dialog("destroy").remove(), _0x3051c2());
          },
          Não: function () {
            $(this).dialog("destroy").remove();
          },
        };
        ($(this).dialog("destroy").remove(),
          dialogConfirm(
            _0x2a449f,
            "Deseja iniciar o processo " + _0x2a449f,
            _0x5869f3,
          ));
      }
    } else
      _0x38f458[0x1] == "false"
        ? ((_0x410a82 += 0x1),
          processaEtapasProgressBar(
            _0x2a449f,
            _0x4a07f2,
            _0x32ab00.length,
            0x0,
            0x0,
          ),
          processaEtapaUnica(
            idProgressBar,
            _0x2a449f,
            _0x41e264,
            _0x38f458[0x0],
            _0x35645c,
            _0x410a82,
            _0x5299a1,
            _0x505bc5,
            _0x23e692,
            _0xf56961,
            _0x22cc82,
          ))
        : processaEtapasLoop(
            idProgressBar,
            0x1,
            _0x2a449f,
            _0x41e264,
            _0x32ab00[_0x410a82],
            _0x35645c,
            _0x410a82,
            _0x5299a1,
            _0x505bc5,
            _0x23e692,
            _0xf56961,
            _0x22cc82,
          );
  }
}
function processaEtapasLoop(
  _0x2936e9,
  _0x1ac389,
  _0x55715c,
  _0x3f4339,
  _0x372c28,
  _0x589ae9,
  _0x2fb257,
  _0x5e1e78,
  _0x2e1940,
  _0x4121f9,
  _0x31763f,
  _0x5ad715,
) {
  var _0x2e7970 = _0x5e1e78.split(";"),
    _0x108988 = _0x2fb257 + 0x1;
  $.getJSON(
    "/sistema/outros/grid_func-ajax/ajaxEtapas/?ajax=true&inicio=" +
      _0x1ac389 +
      "&classe=" +
      _0x3f4339 +
      "&metodo=" +
      encodeURIComponent(_0x372c28) +
      "&sql=" +
      encodeURIComponent(_0x589ae9) +
      "&callback=?",
    function (_0x1ce370) {
      _0x1ce370.code
        ? _0x1ce370.concluido == 0x0
          ? (processaEtapasProgressBar(
              _0x55715c,
              _0x108988,
              _0x2e7970.length,
              _0x1ce370.percentual,
              _0x1ce370.ok,
            ),
            processaEtapasLoop(
              _0x2936e9,
              0x0,
              _0x55715c,
              _0x3f4339,
              _0x372c28,
              _0x589ae9,
              _0x2fb257,
              _0x5e1e78,
              _0x2e1940,
              _0x4121f9,
              _0x31763f,
              _0x5ad715,
            ))
          : ((_0x2fb257 += 0x1),
            _0x2e7970.length <= _0x2fb257
              ? ($("#dc-" + _0x2936e9)
                  .dialog("destroy")
                  .remove(),
                processaEtapasProgressBar(
                  _0x55715c,
                  _0x108988,
                  _0x2e7970.length,
                  _0x1ce370.percentual,
                  _0x1ce370.ok,
                  true,
                ),
                divRefresh(_0x2e1940),
                typeof _0x4121f9 === "function" && _0x4121f9(_0x1ce370))
              : processaEtapas(
                  _0x55715c,
                  _0x3f4339,
                  _0x5e1e78,
                  _0x589ae9,
                  _0x2fb257,
                  _0x2e1940,
                  _0x4121f9,
                  _0x31763f,
                  _0x5ad715,
                ))
        : ($("#dc-" + _0x2936e9)
            .dialog("destroy")
            .remove(),
          dialogConfirm(_0x55715c, _0x1ce370.erros),
          typeof _0x31763f === "function" && _0x31763f(_0x1ce370));
    },
  );
}
function processaEtapaUnica(
  _0x49b10a,
  _0x33b841,
  _0xd80aef,
  _0x2f1d13,
  _0x3822bd,
  _0x21e4dd,
  _0x4c23ad,
  _0x12a690,
  _0x5899aa,
  _0x1ada3f,
  _0x2be6b6,
) {
  var _0x31eb7f = _0x4c23ad.split(";");
  (console.log("aMetodos.length " + _0x31eb7f.length),
    $.getJSON(
      "/sistema/outros/grid_func-ajax/ajaxEtapaUnica/?ajax=true&classe=" +
        _0xd80aef +
        "&metodo=" +
        encodeURIComponent(_0x2f1d13) +
        "&sql=" +
        encodeURIComponent(_0x3822bd) +
        "&callback=?",
      function (_0x39b06a) {
        _0x39b06a.code
          ? ((_0x21e4dd += 0x1),
            _0x31eb7f.length <= _0x21e4dd
              ? ($("#dc-" + _0x49b10a)
                  .dialog("destroy")
                  .remove(),
                console.log("terminou"),
                processaEtapasProgressBar(
                  _0x33b841,
                  _0x21e4dd,
                  _0x31eb7f.length,
                  0x64,
                  0x0,
                  true,
                ),
                divRefresh(_0x12a690),
                typeof _0x5899aa === "function" && _0x5899aa(_0x39b06a))
              : processaEtapas(
                  _0x33b841,
                  _0xd80aef,
                  _0x4c23ad,
                  _0x3822bd,
                  _0x21e4dd,
                  _0x12a690,
                  _0x5899aa,
                  _0x1ada3f,
                  _0x2be6b6,
                ))
          : ($("#dc-" + _0x49b10a)
              .dialog("destroy")
              .remove(),
            dialogConfirm(_0x33b841, _0x39b06a.erros),
            typeof _0x1ada3f === "function" && _0x1ada3f(_0x39b06a));
      },
    ));
}
function processaEtapasAsync(
  _0x131bbe,
  _0x474a4d,
  _0x519244,
  _0x4d3c0c,
  _0x22b11e,
  _0x5861f4,
  _0x70c5a0,
) {
  return new Promise(function (_0x58f88b, _0x5b84d2) {
    processaEtapas(
      _0x131bbe,
      _0x474a4d,
      _0x519244,
      _0x4d3c0c,
      _0x22b11e || 0x0,
      _0x5861f4,
      function (_0x4fb17d) {
        _0x58f88b(_0x4fb17d || true);
      },
      function (_0x22dd9b) {
        _0x5b84d2(_0x22dd9b || new Error("Erro ao processar etapas"));
      },
      _0x70c5a0 === true,
    );
  });
}
function processaEtapasProgressBar(
  _0x10b612,
  _0x3bc630,
  _0x5c4533,
  _0x1e2184,
  _0x51abc9,
  _0xaeedbd,
) {
  _0xaeedbd === true
    ? _0x51abc9 > 0x0
      ? dialogConfirm(
          _0x10b612,
          "<br />Processado: " +
            _0x51abc9 +
            " registro(s) = " +
            _0x1e2184 +
            "% concluído",
        )
      : dialogConfirm(
          _0x10b612,
          "<br />Processado: " + _0x1e2184 + "% concluído",
        )
    : $("#progressBar").html(
        "<br />Processo " +
          _0x3bc630 +
          "/" +
          _0x5c4533 +
          ": " +
          _0x51abc9 +
          " registro(s)  = " +
          _0x1e2184 +
          "% concluído",
      );
}
function processarEtapasGrid(
  _0x11d6cc,
  _0x1ce9cc,
  _0x4cc60a,
  _0x158b6f,
  _0x1ff7ff,
  _0x1b21e1,
  _0x510380,
  _0x4e2c55,
  _0xd990db,
  _0x27f19c,
  _0xa7d17,
) {
  if (_0x1ce9cc == 0x0) {
    alert("Nenhum registro selecionado no grid.");
    return;
  }
  var _0x44d962 =
      " Total " +
      _0x1ce9cc +
      " " +
      (_0x1ce9cc == 0x1 ? "registro" : "registros") +
      ".",
    _0x4a878b = _0xa7d17
      ? _0xa7d17 + _0x44d962
      : "Deseja iniciar o processamento?" + _0x44d962,
    _0x3317bd = {
      Sim: function () {
        ($(this).dialog("destroy").remove(),
          (idProgressBar = dialogAlert(
            _0x11d6cc,
            "<div id='progressBar' style='margin: 40px 10px; display: flex'><div><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div><div style='margin-left: 10px; line-height: 32px;'>Processando registros: <span id='" +
              _0x4cc60a +
              "'>" +
              0x1 +
              "/" +
              _0x1ce9cc +
              " = 0%</span></div></div>",
          )),
          processaEtapasLoopGrid(
            idProgressBar,
            0x0,
            _0x11d6cc,
            _0x1ce9cc,
            _0x4cc60a,
            _0x158b6f,
            _0x1ff7ff,
            _0x1b21e1,
            _0x510380,
            _0x4e2c55,
            _0xd990db,
            _0x27f19c,
          ));
      },
      Não: function () {
        $(this).dialog("destroy").remove();
      },
    };
  ($(this).dialog("destroy").remove(),
    dialogConfirm(_0x11d6cc, _0x4a878b, _0x3317bd));
}
function processaEtapasLoopGrid(
  _0x2fd4fa,
  _0x1b42bf,
  _0x809904,
  _0x667fc1,
  _0x4189e2,
  _0x4186d8,
  _0x25e29a,
  _0x51e356,
  _0x3015f9,
  _0x398d13,
  _0xa50625,
  _0x9c5334,
) {
  const _0x21b61d = _0x51e356 ? $("#" + _0x51e356).data() : [];
  (typeof _0x21b61d.divId == "undefined" &&
    ((_0x21b61d.divId = _0x21b61d.janelaId),
    (_0x21b61d.divIdChain = ""),
    (_0x21b61d.divIdRoot = _0x21b61d.janelaIdRoot),
    (_0x21b61d.divIdParent = _0x21b61d.janelaIdParent),
    (_0x21b61d.divIdParentChain = _0x21b61d.janelaIdParentChain)),
    $.ajax({
      url: "/sistema/outros/grid_func-ajax/ajaxEtapasGrid/",
      dataType: "jsonp",
      data:
        "ajax=true&registro=" +
        _0x1b42bf +
        "&gridId=" +
        _0x4186d8 +
        "&id=" +
        _0x25e29a +
        "&cacheDados=" +
        encodeURIComponent(_0x4189e2) +
        "&lote=" +
        _0x3015f9 +
        "&camposForm=" +
        encodeURIComponent(_0x398d13) +
        ("&divId=" +
          _0x21b61d.divId +
          "&divIdChain=" +
          _0x21b61d.divIdChain +
          "&divIdRoot=" +
          _0x21b61d.divIdRoot +
          "&divIdParent=" +
          _0x21b61d.divIdParent +
          "&divIdParentChain=" +
          _0x21b61d.divIdParentChain +
          "&uniqId=") +
        _0x9c5334 +
        _0xa50625 +
        "&callback=?",
      type: "post",
      success: function (_0x3f2b76) {
        if (_0x3015f9 == "js")
          $("#" + _0x51e356).append(
            '<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != \'undefined\') { ' +
              _0x3f2b76 +
              " } </script></div>",
          );
        else
          _0x3f2b76.code
            ? !_0x3f2b76.concluido
              ? ($("#" + _0x4189e2).html(
                  _0x1b42bf +
                    (_0x3015f9 > 0x0 ? _0x3015f9 : 0x1) +
                    "/" +
                    _0x667fc1 +
                    " = " +
                    Math.round((_0x1b42bf / _0x667fc1) * 0x64) +
                    "%",
                ),
                (_0x1b42bf = _0x1b42bf + _0x3015f9),
                processaEtapasLoopGrid(
                  _0x2fd4fa,
                  _0x1b42bf,
                  _0x809904,
                  _0x667fc1,
                  _0x4189e2,
                  _0x4186d8,
                  _0x25e29a,
                  _0x51e356,
                  _0x3015f9,
                  _0x398d13,
                  _0xa50625,
                  _0x9c5334,
                ))
              : ($("#dc-" + _0x2fd4fa)
                  .dialog("destroy")
                  .remove(),
                typeof _0x3f2b76.data === "undefined" &&
                  (_0x3f2b76.data = "Finalizado"),
                dialogConfirm(_0x809904, _0x3f2b76.data),
                _0x51e356 && divRefresh(_0x51e356, true),
                typeof _0x3f2b76.janela != "undefined" &&
                  abrirJanela(
                    "",
                    divIdRootAberto(),
                    divIdRootAberto(),
                    unique(),
                    "",
                    _0x809904,
                    _0x3f2b76.janela,
                    _0x3f2b76.janelaParametros,
                    [0x3e8, 0x262],
                  ))
            : ($("#dc-" + _0x2fd4fa)
                .dialog("destroy")
                .remove(),
              dialogConfirm(
                _0x809904,
                '<span style="color:red">Ocorreu um erro:</span>&nbsp;' +
                  _0x3f2b76.data,
              ));
      },
    }));
}
function editarTotais(_0x52d574, _0x343592) {
  var _0xc79ccb = gridPegarDivId(_0x52d574.target),
    _0x57e029 = $("#" + _0xc79ccb);
  abrirJanela(
    _0x52d574,
    divIdRootAberto(),
    _0xc79ccb,
    unique(),
    "",
    "Totais",
    "/sistema/personalizacaoLowCode/content/editarTotais/",
    "&gridId=" +
      encodeURIComponent(_0x57e029.data("md5")) +
      "&acaoId=" +
      (_0x343592 ? encodeURIComponent(_0x343592) : "") +
      "&path=" +
      encodeURIComponent(_0x57e029.data("href")),
    [0x2bc, 0x1c2],
    false,
    false,
    "center",
  );
}
function editarBotao(_0x391bdb, _0x24606e) {
  var _0x280aca = gridPegarDivId(
      $(_0x391bdb.target).closest(".frameworkBotoes"),
    ),
    _0x5edd78 = $("#" + _0x280aca),
    _0x312b03 = _0x5edd78.data("href");
  (typeof _0x312b03 == "undefined" &&
    (_0x312b03 = _0x5edd78.data("janelaHref")),
    abrirJanela(
      _0x391bdb,
      divIdRootAberto(),
      _0x280aca,
      unique(),
      "",
      "Botões",
      "/sistema/personalizacaoLowCode/content/editarBotao/",
      "&acaoId=" +
        (_0x24606e ? encodeURIComponent(_0x24606e) : "") +
        "&path=" +
        encodeURIComponent(_0x312b03),
      [0x320, 0x258],
      false,
      false,
      "center",
    ));
}
function editarBotaoExibirCallback(_0x4db640) {
  var _0x80428d = gridPegarDivId(_0x4db640.target),
    _0x5e56a6 = $("#" + _0x80428d);
  abrirJanela(
    _0x4db640,
    divIdRootAberto(),
    _0x80428d,
    unique(),
    "",
    "Exibir via Regra",
    "/sistema/personalizacaoLowCode/content/editarBotaoExibirCallback/",
    "",
    [0x30c, 0x12c],
    false,
    false,
    "center",
  );
}
function executarCallbackRegistroGrid(
  _0x3b119f,
  _0x2d1f7d,
  _0x5e13e7,
  _0x441157,
  _0x4f9be7,
  _0x2dd365,
  _0x4495e5,
  _0x50341c,
  _0x2ca67f,
  _0x382902,
) {
  var _0x24d32a = $(this),
    _0x4414dc = unique(),
    _0x4d8021 =
      typeof _0x3b119f === "object"
        ? gridPegarDivId($(_0x3b119f.target).closest(".frameworkBotoes"))
        : _0x3b119f,
    _0x4161ef = $("#" + _0x4d8021);
  if (_0x4495e5) {
    const _0x442960 = {
      titulo: _0x2d1f7d,
      gridId: _0x5e13e7,
      id: _0x441157,
      recarregar: _0x4f9be7,
      lote: _0x2dd365,
      divId: _0x4d8021,
      parametros: _0x2ca67f,
      confirmacao: _0x382902,
    };
    ((_0x4495e5 = _0x4495e5.split(";")),
      abrirJanela(
        false,
        divIdRootAberto(),
        "",
        menuId("botaoGridForm"),
        "",
        "Entrada de parâmetros",
        _0x4495e5[0x0].replace("/form/", "/content/"),
        "&executarCallbackRegistroGrid=" +
          encodeURIComponent(JSON.stringify(_0x442960)),
        _0x4495e5[0x1] ? JSON.parse(_0x4495e5[0x1]) : [0x2bc, 0xc8],
      ));
    return;
  }
  _0x24d32a.prop("disabled", true);
  if (_0x2dd365 === 0x0) {
    var _0x494fd1 = _0x382902 ? _0x382902 : "Deseja iniciar o processamento?",
      _0x3ea37c = {
        Sim: function () {
          ($(this).dialog("destroy").remove(),
            (idProgressBar = dialogAlert(
              _0x2d1f7d,
              "<div id='progressBar' style='margin: 40px 10px; display: flex'><div><img src='/hardness3/static/img/loading.gif' style='width: 24px; height: 24px; margin: 6px; float: left;' /></div><div style='margin-left: 10px; line-height: 32px;'>Processando...</div></div>",
            )),
            processaEtapasLoopGrid(
              idProgressBar,
              0x0,
              _0x2d1f7d,
              false,
              false,
              _0x5e13e7,
              _0x441157,
              _0x4d8021,
              _0x2dd365,
              _0x50341c,
              _0x2ca67f,
              _0x4414dc,
            ));
        },
        Não: function () {
          $(this).dialog("destroy").remove();
        },
      };
    ($(this).dialog("destroy").remove(),
      dialogConfirm(_0x2d1f7d, _0x494fd1, _0x3ea37c));
  } else {
    if (_0x2dd365 === "js") {
      if (_0x382902) {
        var _0x3ea37c = {
          Sim: function () {
            ($(this).dialog("destroy").remove(),
              processaEtapasLoopGrid(
                false,
                0x0,
                _0x2d1f7d,
                false,
                false,
                _0x5e13e7,
                _0x441157,
                _0x4d8021,
                _0x2dd365,
                _0x50341c,
                _0x2ca67f,
                _0x4414dc,
              ));
          },
          Não: function () {
            $(this).dialog("destroy").remove();
          },
        };
        ($(this).dialog("destroy").remove(),
          dialogConfirm(_0x2d1f7d, _0x382902, _0x3ea37c));
      } else
        processaEtapasLoopGrid(
          false,
          0x0,
          _0x2d1f7d,
          false,
          false,
          _0x5e13e7,
          _0x441157,
          _0x4d8021,
          _0x2dd365,
          _0x50341c,
          _0x2ca67f,
          _0x4414dc,
        );
    } else
      (divSetarAcaoId(_0x4d8021, _0x4414dc, "cacheDados"),
        divRefresh(
          _0x4161ef.attr("id"),
          true,
          function (_0xcbad13) {
            var _0x4c19ad = _0xcbad13.match(/cacheDados \[([0-9]+)\]/);
            _0x4c19ad &&
              ((_0x4c19ad = _0x4c19ad[0x1]),
              divSetarAcaoId(_0x4d8021, "", "cacheDados"),
              processarEtapasGrid(
                _0x2d1f7d,
                _0x4c19ad,
                _0x4414dc,
                _0x5e13e7,
                _0x441157,
                _0x4f9be7 ? _0x4d8021 : false,
                _0x2dd365,
                _0x50341c,
                _0x2ca67f,
                _0x4414dc,
                _0x382902,
              ));
          },
          "pular",
        ));
  }
}
function acaoClickLinha(_0x26775b, _0x176ee8) {
  let _0x428d0d = false;
  $("#" + _0x26775b + " .grid tr")
    .unbind("click")
    .unbind("click")
    .bind("click", function (_0x3e9175) {
      if (_0x428d0d) returnfalse;
      ((_0x428d0d = true),
        setTimeout(function () {
          _0x428d0d = false;
        }, 0x1f4));
      if (
        _0x3e9175.target.nodeName != "TD" &&
        _0x3e9175.target.nodeName != "TR"
      )
        returntrue;
      const _0x4f0e6b = JSON.parse(
          $(this).closest(".linha").attr("todosCampos"),
        ),
        _0x4c984a = gridPegarDivId($(this)),
        _0x30a79f = {};
      let _0x276425 = $("#" + _0x4c984a)
        .data("vars")
        .split("&");
      ($.each(_0x276425, function (_0x18b0c1, _0x3c2e35) {
        let _0x2bc665 = _0x3c2e35.split("=");
        if (!_0x2bc665[0x0]) return;
        _0x30a79f[_0x2bc665[0x0]] = _0x2bc665[0x1];
      }),
        _0x176ee8(_0x3e9175, {
          todosCampos: _0x4f0e6b,
          divId: $("#" + _0x4c984a).data("divId"),
          divIdChain: $("#" + _0x4c984a).data("divIdChain"),
          divIdRoot: $("#" + _0x4c984a).data("divIdRoot"),
          divIdParent: $("#" + _0x4c984a).data("divIdParent"),
          divIdParentChain: $("#" + _0x4c984a).data("divIdParentChain"),
          href: $("#" + _0x4c984a).data("href"),
          md5: $("#" + _0x4c984a).data("md5"),
          parametros: _0x30a79f,
        }));
    });
}
function acaoFecharJanela(_0x8d6108, _0x33d5da) {
  $("#" + _0x8d6108)
    .closest(".ui-dialog-content")
    .bind("dialogclose", _0x33d5da);
}
function getDivIdTab(_0x318bd2) {
  if (_0x318bd2.find("> thead").size() != 0x0)
    var _0x2a9b7d = _0x318bd2.attr("id");
  else {
    if (_0x318bd2.closest(".content-2rows").size() != 0x0) {
      var _0x2a9b7d = _0x318bd2.closest(".content-2rows").attr("id");
      if (
        _0x318bd2
          .parent()
          .parent()
          .parent()
          .parent()
          .parent()
          .hasClass("ui-tabs-panel")
      ) {
        var _0x2a9b7d = _0x318bd2.closest(".ui-tabs-panel").attr("id");
        if (_0x318bd2.closest(".ui-tabs-panel").find(".content").size() != 0x0)
          var _0x2a9b7d = _0x318bd2
            .closest(".ui-tabs-panel")
            .find(".content")
            .attr("id");
      }
    } else {
      if (_0x318bd2.closest(".content-3rows").size() != 0x0) {
        var _0x2a9b7d = _0x318bd2.closest(".content-3rows").attr("id");
        if (
          _0x318bd2
            .parent()
            .parent()
            .parent()
            .parent()
            .parent()
            .hasClass("ui-tabs-panel")
        ) {
          var _0x2a9b7d = _0x318bd2.closest(".ui-tabs-panel").attr("id");
          if (
            _0x318bd2.closest(".ui-tabs-panel").find(".content").size() != 0x0
          )
            var _0x2a9b7d = _0x318bd2
              .closest(".ui-tabs-panel")
              .find(".content")
              .attr("id");
        }
      } else {
        if (_0x318bd2.closest(".ui-tabs-panel").size() != 0x0) {
          var _0x2a9b7d = _0x318bd2.closest(".ui-tabs-panel").attr("id");
          if (
            _0x318bd2.closest(".ui-tabs-panel").find(".content").size() != 0x0
          )
            var _0x2a9b7d = _0x318bd2
              .closest(".ui-tabs-panel")
              .find(".content")
              .attr("id");
        } else var _0x2a9b7d = _0x318bd2.closest(".content").attr("id");
      }
    }
  }
  return _0x2a9b7d;
}
function removerElementosEditorTela(_0x16bae6) {
  return (
    _0x16bae6.find(".moveHandle").remove(),
    _0x16bae6.find(".telaItemWidth").remove(),
    _0x16bae6.find(".telaItemHeight").remove(),
    _0x16bae6.find(".ui-draggable").each(function () {
      $(this).removeClass("ui-draggable");
    }),
    _0x16bae6.find(".telaItem").each(function () {
      $(this).css({ cursor: "" });
    }),
    _0x16bae6.find(".editorTelaReguaVertical").parent().remove(),
    $("p").each(function () {
      $(this)
        .html()
        .replace(/\s|&nbsp;/g, "").length == 0x0 && $(this).remove();
    }),
    _0x16bae6
  );
}
function wikiAjuda(_0xa61139, _0x44ab49, _0x3fad9d, _0x49550f, _0x3dfa67) {
  ($(".colunaAcoes").remove(),
    $(".list-options-form").remove(),
    ($elem = $("#" + _0xa61139)),
    ($janela = $elem.closest(".ui-dialog-content")),
    (titulo = $elem.data("titleTab")));
  !titulo && (titulo = $elem.closest(".ui-tabs-panel").data("titleTab"));
  !titulo && (titulo = "?");
  var _0x382337 =
    "&janelaTipo=JANELA" +
    "&janelaHref=" +
    encodeURIComponent($janela.data("janelaHref")) +
    "&janelaTitulo=" +
    encodeURIComponent($janela.data("janelaTituloWin"));
  _0x44ab49 &&
    (_0x382337 +=
      "&itemTipo=" +
      encodeURIComponent(_0x44ab49) +
      "&itemHref=" +
      encodeURIComponent($elem.data("href")) +
      "&itemTitulo=" +
      encodeURIComponent(titulo));
  _0x3fad9d &&
    (_0x382337 +=
      "&subitemTipo=" +
      encodeURIComponent(_0x3fad9d) +
      "&subitemHref=" +
      encodeURIComponent(_0x49550f) +
      "&subitemTitulo=" +
      encodeURIComponent(_0x3dfa67));
  const _0x26f428 = $elem.data("divIdRoot")
    ? $elem.data("divIdRoot")
    : divIdRootAberto();
  abrirJanela(
    false,
    _0x26f428,
    _0xa61139,
    unique(),
    "",
    "Ajuda",
    "/etc/etc019/outro/etc019OutroAjuda/",
    _0x382337,
  );
}
function abrirJanelaEnviarEmail(_0x2e1386) {
  abrirJanela(
    false,
    divIdRootAberto(),
    false,
    unique(),
    "",
    "Enviar um Email",
    "/etc/etc020/content/etc020ContentEnviarEmail/",
    "&options=" + encodeURIComponent(JSON.stringify(_0x2e1386)),
    [0x384, 0x226],
  );
}
function modoDesenvolvedorApp(_0x1b603d) {
  $.getJSON(
    "/sistema/ajax/verificaRoot/?ajax=true&callback=?",
    function (_0x25368c) {
      _0x25368c.code
        ? $.getJSON(
            "/sistema/ajax/modoDesenvolvedorApp/?ajax=true&appId=" +
              encodeURIComponent(_0x1b603d) +
              "&callback=?",
            function (_0x1296fd) {
              _0x1296fd.code ? location.reload() : alert(_0x1296fd.data);
            },
          )
        : alert(
            "Acesso negado! Somente o administrador tem acesso a esta opção.",
          );
    },
  );
}
function atualizarLinhaGrid(_0x387a6d) {
  divRefresh(
    _0x387a6d,
    true,
    false,
    false,
    $("#" + _0x387a6d + " .grid .linhaSelecionada:first").attr("id"),
    false,
  );
}
function divIdContentAcima(_0x3af9c8) {
  return $("#" + _0x3af9c8)
    .prev(".content")
    .attr("id");
}
function divIdContentAbaixo(_0x4c8206) {
  return $("#" + _0x4c8206)
    .next(".content")
    .attr("id");
}
function abortarRequisicoesAjax(_0x8333ef) {
  if (typeof requisicoesAjax[_0x8333ef.attr("id")] !== "undefined")
    for (const _0x2ab5bd of requisicoesAjax[_0x8333ef.attr("id")]) {
      _0x2ab5bd.abort();
    }
  _0x8333ef.find(".content, .ui-tabs-panel").each(function () {
    if (typeof requisicoesAjax[$(this).attr("id")] !== "undefined")
      for (const _0x3e9689 of requisicoesAjax[$(this).attr("id")]) {
        _0x3e9689.abort();
      }
  });
}
function updateGridHeadersWidth(_0x293a4a) {
  (_0x293a4a.css("table-layout", "auto"),
    _0x293a4a.find("thead:first th").css("width", ""),
    _0x293a4a.find("thead:first th").each(function () {
      var _0x25d2a2 = $(this),
        _0x51db23 = _0x25d2a2.width();
      _0x25d2a2.css("width", _0x51db23 + "px");
    }),
    _0x293a4a.css("table-layout", "fixed"));
}
function getFullHeight(_0x53336a) {
  if (_0x53336a.length == 0x0) return 0x0;
  const _0x1734e8 = _0x53336a[0x0],
    _0x44ecee = _0x1734e8.getBoundingClientRect(),
    _0x4e73e6 = window.getComputedStyle(_0x1734e8),
    _0x4b1d76 = (_0x5906d3) => parseFloat(_0x5906d3) || 0x0,
    _0x517506 = _0x4b1d76(_0x4e73e6.marginTop),
    _0x46abe9 = _0x4b1d76(_0x4e73e6.marginBottom);
  return _0x44ecee.height + _0x517506 + _0x46abe9;
}
function getExtraVerticalSpace(_0xeef4c8) {
  if (_0xeef4c8.length == 0x0) return 0x0;
  const _0x563726 = _0xeef4c8[0x0],
    _0x46ec7e = window.getComputedStyle(_0x563726),
    _0x26578e = (_0x58593a) => parseFloat(_0x58593a) || 0x0;
  return (
    _0x26578e(_0x46ec7e.marginTop) +
    _0x26578e(_0x46ec7e.marginBottom) +
    _0x26578e(_0x46ec7e.borderTopWidth) +
    _0x26578e(_0x46ec7e.borderBottomWidth) +
    _0x26578e(_0x46ec7e.paddingTop) +
    _0x26578e(_0x46ec7e.paddingBottom)
  );
}
function getExtraHorizontalSpace(_0x400103) {
  if (_0x400103.length == 0x0) return 0x0;
  const _0x3a4634 = _0x400103[0x0],
    _0xddbb5d = window.getComputedStyle(_0x3a4634),
    _0x37eb01 = (_0x4c69da) => parseFloat(_0x4c69da) || 0x0;
  return (
    _0x37eb01(_0xddbb5d.marginLeft) +
    _0x37eb01(_0xddbb5d.marginRight) +
    _0x37eb01(_0xddbb5d.borderLeftWidth) +
    _0x37eb01(_0xddbb5d.borderRightWidth) +
    _0x37eb01(_0xddbb5d.paddingLeft) +
    _0x37eb01(_0xddbb5d.paddingRight)
  );
}
async function copyToClipboard(_0xf5752d) {
  const _0x19bc86 = $("<textarea>");
  ($("body").append(_0x19bc86), _0x19bc86.val(_0xf5752d).select());
  try {
    const _0x399e53 = document.execCommand("copy");
    return _0x399e53;
  } catch (_0x132081) {
    return (console.error("execCommand error:", _0x132081), false);
  } finally {
    _0x19bc86.remove();
  }
}
function maximizeDialog(_0x2914f6, _0x3f87e3 = false) {
  var _0x4e1ae3 = _0x2914f6.find(".ui-dialog-content:first"),
    _0x7299e1 = parseFloat(
      getComputedStyle(_0x4e1ae3[0x0])
        .getPropertyValue("width")
        .replace("px", ""),
    ),
    _0x55168e = parseFloat(
      getComputedStyle(_0x4e1ae3[0x0])
        .getPropertyValue("height")
        .replace("px", ""),
    );
  _0x2914f6.hide();
  var _0x547c9c = _0x4e1ae3.data("minWidth"),
    _0x1c3820 = _0x4e1ae3.data("minHeight");
  if (
    g.tamanhosJanelas[_0x4e1ae3.data("janelaHref")] != "full" ||
    _0x3f87e3 ||
    _0x4e1ae3.data("forceFull") == true
  )
    (_0x4e1ae3.data("forceFull", ""),
      _0x2914f6.addClass("is-maximized"),
      _0x4e1ae3.dialog(
        "option",
        "width",
        desktopWidth - (desktopLeft + desktopRight) - dialogBorder * 0x4,
      ),
      _0x4e1ae3.dialog(
        "option",
        "height",
        desktopHeight - dialogBorder * 0x6 + 0x7,
      ),
      (g.tamanhosJanelas[_0x4e1ae3.data("janelaHref")] = "full"),
      _0x2914f6.css({
        display: "flex",
        "flex-direction": "column",
        height:
          "calc(100% - " +
          desktopUp +
          "px - " +
          getExtraVerticalSpace(_0x2914f6) +
          "px)",
        width:
          "calc(100% - " +
          desktopLeft +
          "px - " +
          getExtraHorizontalSpace(_0x2914f6) +
          "px)",
        top: desktopUp,
        left: desktopLeft,
      }),
      _0x4e1ae3.css({ flex: "1" }));
  else {
    var _0xed8837 = false,
      _0x21d424 = false;
    (_0x2914f6.removeClass("is-maximized"),
      _0x4e1ae3.data("originalHeight", _0x55168e),
      _0x4e1ae3.data("originalWidth", _0x7299e1),
      _0x547c9c > desktopWidth &&
        ((_0xed8837 = true),
        (_0x547c9c = desktopWidth - getExtraHorizontalSpace(_0x2914f6))),
      _0x4e1ae3.dialog("option", "width", _0x547c9c),
      _0x1c3820 > desktopHeight &&
        ((_0x21d424 = true),
        (_0x1c3820 = desktopHeight - getExtraVerticalSpace(_0x2914f6))),
      _0x4e1ae3.dialog("option", "height", _0x1c3820),
      _0x4e1ae3.dialog("option", "position", {
        my: "center",
        at: "center",
        of: $("#desktop"),
      }),
      _0xed8837 && _0x21d424
        ? (g.tamanhosJanelas[_0x4e1ae3.data("janelaHref")] = "full")
        : (g.tamanhosJanelas[_0x4e1ae3.data("janelaHref")] = [
            _0x547c9c,
            _0x1c3820,
          ]));
  }
  ($.ajax({
    url: "/sistema/funcoes/tamanhosJanelas/salvar/",
    dataType: "html",
    data:
      "ajax=true&json=" + encodeURIComponent(JSON.stringify(g.tamanhosJanelas)),
    type: "post",
  }),
    _0x2914f6.show(),
    _0x4e1ae3.find(".content:visible").each(function (_0x457c85, _0xb89629) {
      (corrigeConteudo($(this).attr("id")), corrigeGrid($(this).attr("id")));
    }),
    $(".colunaAcoes").remove(),
    $(".list-options-form").remove());
}
function qrCode(_0x463ac1, _0x27caaf, _0x4dbc3e, _0x2f5bf0, _0x356ecc) {
  (console.log("qrCode chamado:", {
    string: _0x463ac1,
    novaJanela: _0x27caaf,
    name: _0x4dbc3e,
    format: _0x2f5bf0,
    size: _0x356ecc,
  }),
    (_0x27caaf = typeof _0x27caaf === "undefined" ? 0x0 : _0x27caaf),
    (_0x2f5bf0 = (_0x2f5bf0 || "svg").toString().toLowerCase()),
    (_0x356ecc = _0x356ecc || 0x12c),
    (_0x4dbc3e = _0x4dbc3e || "qrcode"),
    $.ajax({
      url: "/sistema/personalizacao/outro/e1173362bd6c65f0ac19d23997612331/",
      dataType: "jsonp",
      data: {
        ajax: true,
        string: _0x463ac1,
        name: _0x4dbc3e,
        format: _0x2f5bf0,
        size: _0x356ecc,
        novaJanela: _0x27caaf,
      },
      success: function (_0x27401b) {
        console.log("retorno jsonp:", _0x27401b);
        if (!_0x27401b.code) {
          gGeraAlertMsg("Erro!", _0x27401b.data);
          return;
        }
        var _0x181d95 = _0x27401b.data;
        if (/^https?:\/\//i.test(_0x181d95))
          _0x181d95 = _0x181d95.replace("/erp/", "/hardness3/");
        else {
          _0x181d95[0x0] !== "/" && (_0x181d95 = "/" + _0x181d95);
          if (_0x181d95.indexOf("/erp/") === 0x0)
            _0x181d95 = _0x181d95.replace("/erp/", "/hardness3/");
          else
            !_0x181d95.startsWith("/hardness3/") &&
              (_0x181d95 = "/hardness3" + _0x181d95);
        }
        var _0x27da85 =
          _0x27401b.novaJanela === true ||
          _0x27401b.novaJanela === 0x1 ||
          _0x27401b.novaJanela === "1";
        if (_0x27da85) {
          abrirJanela(
            false,
            divIdRootAberto(),
            "",
            unique(),
            "",
            "QR Code",
            _0x181d95,
            "",
            [_0x356ecc + 0xa, _0x356ecc + 0x28],
            false,
            "center",
          );
          return;
        }
        fetch(_0x181d95, { credentials: "include" })
          .then(async function (_0x103552) {
            if (!_0x103552.ok) throw new Error("HTTP " + _0x103552.status);
            var _0x4be5e0 = await _0x103552.blob(),
              _0x2a1807 = URL.createObjectURL(_0x4be5e0),
              _0x4c7c59 = document.createElement("a"),
              _0x4db60f = _0x181d95.split("/").pop() || "qrcode." + _0x2f5bf0;
            ((_0x4c7c59.href = _0x2a1807),
              (_0x4c7c59.download = _0x4db60f),
              document.body.appendChild(_0x4c7c59),
              _0x4c7c59.click(),
              _0x4c7c59.remove(),
              URL.revokeObjectURL(_0x2a1807));
          })
          ["catch"](function (_0x599fc7) {
            (console.error("Falha ao baixar:", _0x599fc7),
              gGeraAlertMsg("Erro!", "Não foi possível baixar o arquivo."));
          });
      },
      error: function (_0xa134de, _0x48dd34, _0x285421) {
        console.error(
          "AJAX error:",
          _0x48dd34,
          _0x285421,
          _0xa134de && _0xa134de.responseText,
        );
      },
    }));
}
