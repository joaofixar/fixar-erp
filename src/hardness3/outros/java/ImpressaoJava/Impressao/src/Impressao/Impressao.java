package Impressao;

import java.awt.AWTException;
import java.awt.Dimension;
import java.awt.Image;
import java.awt.MenuItem;
import java.awt.PopupMenu;
import java.awt.SystemTray;
import java.awt.Toolkit;
import java.awt.TrayIcon;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.URL;
import java.net.URLConnection;
import java.net.UnknownHostException;
import java.security.PrivilegedActionException;
import java.util.ArrayList;
import javax.print.DocFlavor;
import javax.print.DocPrintJob;
import javax.print.PrintException;
import javax.print.PrintService;
import javax.print.PrintServiceLookup;
import javax.print.SimpleDoc;
import javax.print.attribute.HashPrintRequestAttributeSet;
import javax.print.attribute.PrintRequestAttributeSet;
import javax.print.attribute.standard.Copies;
import javax.swing.JOptionPane;

public class Impressao {
	static boolean executandoGeral = false;
	// private static final long serialVersionUID = 8325569111746288646L;
	private static PrintService impressora;
	private static String printMessage = "";
	private static TelaImpressao frame;
        private static String mensagemRetorno = "Etiquetas Enviadas para a fila de impressão";
	public static String numeroImpressora = "";
	public static String nomeImpressora = "";
	public static String urlEmpresa = "http://192.168.0.104:8080/";

	public static void main(String[] args) throws Exception {
		final TrayIcon trayIcon;

		if (args.length > 0) {
			numeroImpressora = args[0];
			nomeImpressora = args[1];
			urlEmpresa = args[2];
		}

		System.out.println(numeroImpressora);

		if (SystemTray.isSupported()) {

			SystemTray tray = SystemTray.getSystemTray();
			Image image = Toolkit.getDefaultToolkit().getImage("c:\\hardness\\tray.png");

			ActionListener exitListener = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					System.out.println("Exiting...");
					System.exit(0);
				}
			};

			// Criaï¿½ï¿½o do Menu
			PopupMenu popup = new PopupMenu();

			MenuItem imprimirEtiqueta = new MenuItem("Imprimir Etiqueta");
			popup.add(imprimirEtiqueta);

//			MenuItem imprimirMatricial = new MenuItem("Imprimir Matricial");
//			popup.add(imprimirMatricial);

			MenuItem defaultItem = new MenuItem("Exit");

			defaultItem.addActionListener(exitListener);
			popup.add(defaultItem);

			trayIcon = new TrayIcon(image, "Hardness -  Imprimir Etiquetas", popup);

			// Aï¿½ï¿½o ao clicar em um item do menu
			ActionListener impressaoListener = new ActionListener() {
				public void actionPerformed(ActionEvent e) {
					trayIcon.displayMessage("Atenção", mensagemRetorno,
							TrayIcon.MessageType.INFO);
					try {
						Impressao.downloadEtiqueta();
						frame = new TelaImpressao();
						Dimension size = frame.getPreferredSize();
						frame.setVisible(true);
                                                frame.setLocationRelativeTo(null);

					} catch (IOException e1) {
						e1.printStackTrace();
					} catch (Exception e1) {
						e1.printStackTrace();
					}
				}
			};

			imprimirEtiqueta.addActionListener(impressaoListener);

//			ActionListener impressaoMatricialListener = new ActionListener() {
//				public void actionPerformed(ActionEvent e) {
//					trayIcon.displayMessage("Aï¿½ï¿½o", "Imprimindo Etiquetas...", TrayIcon.MessageType.INFO);
//					try {
//						Impressao.downloadEtiqueta();
//					} catch (IOException e1) {
//						e1.printStackTrace();
//					}
//				}
//			};
//
//			imprimirMatricial.addActionListener(impressaoMatricialListener);

			// ï¿½cone
			trayIcon.setImageAutoSize(true);

			try {
				tray.add(trayIcon);
			} catch (AWTException e) {
				System.err.println("TrayIcon could not be added.");
			}

		} else {
			System.err.println(" System Tray is not supported.");
		}
	}

	public static void RemoverTela() throws Exception {
		frame.dispose();
		frame = new TelaImpressao();
		frame.setVisible(true);

	}

	// Seleciona uma impressora por nome
	public static boolean detectaImpressoras(String imp) {
		try {
			DocFlavor df = DocFlavor.SERVICE_FORMATTED.PRINTABLE;
			PrintService[] ps = PrintServiceLookup.lookupPrintServices(df, null);
			boolean achou = false;
			for (PrintService p : ps) {
				System.out.println("Impressora encontrada: " + p.getName());
				if (p.getName().contains(imp)) {
					achou = true;
					impressora = p;
					break;
				}
			}
			if (!achou) {
				printMessage = "Impressora não encontrada!\nPor favor, verifique se ela está configurada e conectada.";
				return false;
			} else {
				return true;
			}
		} catch (Exception e) {
			e.printStackTrace();
			printMessage = "Ocorreu um erro:\n" + e.getMessage();
			return false;
		}
	}

	public static void downloadEtiqueta() throws IOException {

		int contArquivo = 0;
		String line;

		// Pega o nome das etiquetas
		ArrayList<String> nomeEtiqueta = nomeEtiquetas();
                
                if (nomeEtiqueta != null && !nomeEtiqueta.isEmpty()) {
                    for (contArquivo = 0; contArquivo < nomeEtiqueta.size(); contArquivo++) {
                        System.out.println("entra no for");
                            String filePathHardness = "C:\\hardness\\impressao\\" + nomeEtiqueta.get(contArquivo);
                            File baseFolder = new File("C:\\hardness\\impressao\\");
                            if (!baseFolder.exists()) {
                                    baseFolder.mkdirs();
                            }
                            File f = new File(filePathHardness);
                            if (f.exists() && !f.isDirectory()) {
                                    f.delete();
                            }
                            String parametersImprimir = "ajax=true&noEmp=true&p=2&u=63&impressora=" + impressora + "&nomeArquivo="
                                            + nomeEtiqueta.get(contArquivo);
                            URL urlImprimir = new URL(urlEmpresa + "outros/impressao/impressao/imprimir/");
                            URLConnection connImprimir = urlImprimir.openConnection();


                            connImprimir.setDoOutput(true);

                            OutputStreamWriter writerImprimir = new OutputStreamWriter(connImprimir.getOutputStream());

                            writerImprimir.write(parametersImprimir);
                            writerImprimir.flush();

                            BufferedReader readerImprimir = new BufferedReader(new InputStreamReader(connImprimir.getInputStream()));

                            PrintWriter out = new PrintWriter(filePathHardness);
                            while ((line = readerImprimir.readLine()) != null) {
                                    // System.out.println(line);
                                    out.println(line);
                            }
                            writerImprimir.close();
                            readerImprimir.close();
                            out.close();
                    }
                }else{
                    System.out.println("a lista está vazia");
                }

	}

	public static ArrayList<String> nomeEtiquetas() throws UnknownHostException, IOException {
		String nome = "";
		String urlParameters = "ajax=true&noEmp=true&p=2&u=63&impressora=" + numeroImpressora;
		URL url = new URL(urlEmpresa + "outros/impressao/impressao/retornaArquivos/");
		URLConnection conn = url.openConnection();

		conn.setDoOutput(true);
                
                try{
                    OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());
                    System.out.println(writer);
                    writer.write(urlParameters);
                    writer.flush();
                }catch(UnknownHostException ex){
                    JOptionPane.showMessageDialog(null, "Ocorreu um erro de conexão com a url ("+urlEmpresa+")\nVerifique os parâmetros fornecidos no 'impressao.bat'","Atenção!", JOptionPane.ERROR_MESSAGE);
                }


		String line;
		BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

		ArrayList<String> nomeEtiqueta = new ArrayList();

		try {
			while ((line = reader.readLine()) != null) {
				nome = line.substring(line.lastIndexOf("/") + 1, line.length());

				if (nome.indexOf(".etq") != -1) {
					nomeEtiqueta.add(nome);
					System.out.println(nome);
				}
			}
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return nomeEtiqueta;
	}

	public static String imprimirEtiqueta(final String printer, String arquivo) {
		String retorno = null;

		try {
			retorno = java.security.AccessController
					.doPrivileged(new java.security.PrivilegedExceptionAction<String>() {

						public String run() throws IOException {
							int count = 0;
							boolean achou = true;
							String nomeDoArquivo = "C:\\hardness\\impressao\\" + arquivo;
							String conteudo = Impressao.fileToString(nomeDoArquivo);
							String conteudo2[] = conteudo.split(" ");
							byte conteudo3[] = new byte[conteudo2.length];

							for (String s : conteudo2) {
								conteudo3[count++] = (byte) Integer.parseInt(s);
							}

							if (printer.length() == 0) {
								System.out.println("Impressora: Usando padrao.");
								impressora = PrintServiceLookup.lookupDefaultPrintService();
							} else {
								System.out.println("Impressora: Detectando...");
								achou = detectaImpressoras(printer);
							}
							if (achou) {
								// determina os atributos
								PrintRequestAttributeSet printerAttributes = new HashPrintRequestAttributeSet();
								// quantidades de copias ****** opcional
								printerAttributes.add(new Copies(1));
								DocFlavor docFlavor = DocFlavor.BYTE_ARRAY.AUTOSENSE;
								SimpleDoc documentoTexto = new SimpleDoc(conteudo3, docFlavor, null);
								DocPrintJob printJob = impressora.createPrintJob();
								try {
									printJob.print(documentoTexto, (PrintRequestAttributeSet) printerAttributes);
								} catch (PrintException e) {
									// TODO Auto-generated catch block
									e.printStackTrace();
									return "Ocorreu um erro:\n" + e.getMessage();
								}
								File file = new File(nomeDoArquivo);
								file.delete();
								return "Impressãoo concluída!\nO documento foi enviado para a impressora.";

							} else {
								return printMessage;
							}
						}
					});
		} catch (PrivilegedActionException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return retorno;
	}

	public static int quantidadeDeArquivos(File dir) {
		File[] files = dir.listFiles();
		int quantidaEtiquetas = 0;

		for (int i = 0; i < files.length; i++) {
			File File = files[i];
			if (File.getPath().endsWith(".etq")) {
				quantidaEtiquetas++;
			}
		}

		return quantidaEtiquetas;
	}

	public static Object[][] listarEtiquetas(File dir) {
		File[] files = dir.listFiles();
		int quantidaEtiquetas = 0;

		for (int i = 0; i < files.length; i++) {
			File File = files[i];
			if (File.getPath() != null) {
				quantidaEtiquetas++;
			}
		}

		Object[][] dados = new Object[quantidaEtiquetas][3];
		for (int i = 0; i < files.length; i++) {
			File file = files[i];
			if (file.getPath() != null) {
				dados[i][0] = file.getName();
				dados[i][1] = "Imprimir";
				dados[i][2] = "Excluir";
			}
		}
		return dados;
	}

	private static String fileToString(String filename) throws IOException {
		BufferedReader reader = new BufferedReader(new FileReader(filename));
		StringBuilder builder = new StringBuilder();
		String line;

		// For every line in the file, append it to the string builder
		while ((line = reader.readLine()) != null) {
			builder.append(line);
		}
		reader.close();
		return builder.toString();
	}


}
