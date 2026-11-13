import java.awt.AWTException;
import java.awt.CheckboxMenuItem;
import java.awt.Image;
import java.awt.Menu;
import java.awt.MenuItem;
import java.awt.PopupMenu;
import java.awt.SystemTray;
import java.awt.Toolkit;
import java.awt.TrayIcon;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.URL;
import java.net.URLConnection;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.Calendar;
import java.util.Date;
import java.util.Timer;
import java.util.TimerTask;

public class PDV {
	static boolean executandoGeral = false;
	
	public static String urlEmpresa = "http://localhost/";
	public static String empresaAtual = "2";

	public static void main(String[] args) throws IOException {
		final TrayIcon trayIcon;
		
		if (args.length > 0) {
			urlEmpresa = args[0];
			empresaAtual = args[1];
		}
		
		if (SystemTray.isSupported()) {
		
		    SystemTray tray = SystemTray.getSystemTray();
		    Image image = Toolkit.getDefaultToolkit().getImage("c:\\hardness\\tray.png");
		
		   /*MouseListener mouseListener = new MouseListener() {
		                
		        public void mouseClicked(MouseEvent e) {
		            System.out.println("Tray Icon - Mouse clicked!");                 
		        }
		
		        public void mouseEntered(MouseEvent e) {
		            System.out.println("Tray Icon - Mouse entered!");                 
		        }
		
		        public void mouseExited(MouseEvent e) {
		            System.out.println("Tray Icon - Mouse exited!");                 
		        }
		
		        public void mousePressed(MouseEvent e) {
		            System.out.println("Tray Icon - Mouse pressed!");                 
		        }
		
		        public void mouseReleased(MouseEvent e) {
		            System.out.println("Tray Icon - Mouse released!");                 
		        }
		    };*/
		
		    ActionListener exitListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		            //System.out.println("Exiting...");
		            System.exit(0);
		        }
		    };
		            
		    PopupMenu popup = new PopupMenu();
		    
		    MenuItem itemImpPedidos = new MenuItem("Importar Pedidos");
		    popup.add(itemImpPedidos);
		    
		    MenuItem itemImpClientes = new MenuItem("Importar Clientes");
		    popup.add(itemImpClientes);
		    
		    MenuItem itemImpProdutos = new MenuItem("Importar Produtos");
		    popup.add(itemImpProdutos);
		    
		    MenuItem itemImpOps = new MenuItem("Importar Operadores");
		    popup.add(itemImpOps);
		    
		    MenuItem itemCupons = new MenuItem("Exportar Cupons Fiscais");
		    popup.add(itemCupons);
		    
		    MenuItem itemImpVend = new MenuItem("Importar Vendedores");
		    popup.add(itemImpVend);
		    
		    MenuItem defaultItem = new MenuItem("Exit");
		    defaultItem.addActionListener(exitListener);
		    popup.add(defaultItem);
		
		    trayIcon = new TrayIcon(image, "Hardness - Importação/Exportação PDV", popup);
		    
		    ActionListener pedidosListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Importando Pedidos...",
			                TrayIcon.MessageType.INFO);
		        	PDV.importarPedidos();
		        }
		    };
		    itemImpPedidos.addActionListener(pedidosListener);
		    
		    ActionListener clientesListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Importando Clientes...",
			                TrayIcon.MessageType.INFO);
		        	PDV.importarClientes(2);
		        }
		    };
		    itemImpClientes.addActionListener(clientesListener);
		    
		    ActionListener produtosListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Importando Produtos...",
			                TrayIcon.MessageType.INFO);
		        	PDV.importarProdutos();
		        }
		    };
		    itemImpProdutos.addActionListener(produtosListener);
		    
		    ActionListener opsListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Importando Operadores...",
			                TrayIcon.MessageType.INFO);
		        	PDV.importarOperadores();
		        }
		    };
		    itemImpOps.addActionListener(opsListener);
		    
		    ActionListener VendListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Importando Vendedores...",
			                TrayIcon.MessageType.INFO);
		        	PDV.importarVendedores();
		        }
		    };
		    itemImpVend.addActionListener(VendListener);
		    
		    ActionListener cupomListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		        	trayIcon.displayMessage("Ação", 
			                "Exportando Cupons Fiscais...",
			                TrayIcon.MessageType.INFO);
		        	try {
						PDV.enviarPDV();
					} catch (IOException e1) {
						// TODO Auto-generated catch block
						e1.printStackTrace();
					}
		        	trayIcon.displayMessage("Ação ", 
			                "Exportação Concluída!",
			                TrayIcon.MessageType.INFO);
		        }
		    };
		    itemCupons.addActionListener(cupomListener);
		
		    /*ActionListener actionListener = new ActionListener() {
		        public void actionPerformed(ActionEvent e) {
		            trayIcon.displayMessage("Action Event", 
		                "An Action Event Has Been Performed!",
		                TrayIcon.MessageType.INFO);
		        }
		    };*/
		            
		    trayIcon.setImageAutoSize(true);
		    //trayIcon.addActionListener(actionListener);
		    //trayIcon.addMouseListener(mouseListener);
		
		    try {
		        tray.add(trayIcon);
		    } catch (AWTException e) {
		        System.err.println("TrayIcon could not be added.");
		    }
		    
		    // Timer
		    Timer timer = new Timer ();
		    TimerTask hourlyTask = new TimerTask () {
		        @Override
		        public void run () {
		            trayIcon.displayMessage("Ação Automática", 
		                "Executando Importação e Exportação dos dados...",
		                TrayIcon.MessageType.INFO);
		            PDV.executandoGeral = true;
		            PDV.importarClientes(1);
		            try {
		                Thread.sleep(60000);                 // 1000 milliseconds is one second.
		            } catch(InterruptedException ex) {
		                Thread.currentThread().interrupt();
		            }
		            PDV.executandoGeral = false;
		            PDV.importarOperadores();
		            PDV.importarProdutos();
		            PDV.importarVendedores();
		            
		        }
		    };
		    
		    // Get the Date
			Calendar calendar = Calendar.getInstance();
			calendar.set(Calendar.HOUR_OF_DAY, 20);
			calendar.set(Calendar.MINUTE, 1);
			calendar.set(Calendar.SECOND, 1);
			Date time = calendar.getTime();

		    // schedule the task to run starting now and then every ...
		    timer.schedule (hourlyTask, time, 1000*60*60*24); // 1000*60*60*24
		    
    		// Timer
		    Timer timer2 = new Timer ();
		    TimerTask hourlyTask2 = new TimerTask () {
		        @Override
		        public void run () {
		            try {
						PDV.enviarPDV();
					} catch (IOException e) {
						// TODO Auto-generated catch block
						e.printStackTrace();
					}
		        }
		    };
		    timer2.schedule (hourlyTask2, 0l, 1000*30);
		    
		    // Timer
		    Timer timer3 = new Timer ();
		    TimerTask hourlyTask3 = new TimerTask () {
		        @Override
		        public void run () {
		        	PDV.importarPedidos();
		        }
		    };
		    timer3.schedule (hourlyTask3, 0l, 1000*16);
		    
		    // Timer
		    /*Timer timer4 = new Timer ();
		    TimerTask hourlyTask4 = new TimerTask () {
		        @Override
		        public void run () {
		        	if (!PDV.executandoGeral) {
		        		PDV.importarClientes(2);
		        	}
		        }
		    };
		    timer4.schedule (hourlyTask4, 0l, 1000*14);*/
		
		} else {
		
		    //  System Tray is not supported
		
		}
        
	}
	
	private static void importarClientes(int tipo) {
		String filePath = "C:\\sgloja\\importacao\\clientes.txt";
		File f = new File(filePath);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		String filePathHardness = "C:\\hardness\\clientes.txt";
		f = new File(filePathHardness);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		try {
			String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63";
			if (tipo == 2) {
				urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63&hoje=true";
			}
			URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/exportarClientes/");
			URLConnection conn = url.openConnection();

			conn.setDoOutput(true);

			OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

			writer.write(urlParameters);
			writer.flush();

			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

			PrintWriter out = new PrintWriter(filePathHardness);
			while ((line = reader.readLine()) != null) {
				//System.out.println(line);
				out.println(line);
			}
			writer.close();
			reader.close();
			out.close();
			
			// Move
			File afile = new File(filePathHardness);
			afile.renameTo(new File(filePath));
		} catch (Exception e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
	
	private static void importarProdutos() {
		String filePath = "C:\\sgloja\\importacao\\produtos.txt";
		File f = new File(filePath);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		String filePathHardness = "C:\\hardness\\produtos.txt";
		f = new File(filePathHardness);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		try {
			String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63";
			URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/exportarProdutos/");
			URLConnection conn = url.openConnection();

			conn.setDoOutput(true);

			OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

			writer.write(urlParameters);
			writer.flush();

			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));
			
			PrintWriter out = new PrintWriter(filePathHardness);
			while ((line = reader.readLine()) != null) {
				//System.out.println(line);
				out.println(line);
			}
			writer.close();
			reader.close();
			out.close();
			
			// Move
			File afile = new File(filePathHardness);
			afile.renameTo(new File(filePath));
		} catch (Exception e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
	
	private static void importarOperadores() {
		String filePath = "C:\\sgloja\\importacao\\operadores.txt";
		File f = new File(filePath);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		String filePathHardness = "C:\\hardness\\operadores.txt";
		f = new File(filePathHardness);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		try {
			String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63";
			URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/exportarOperadores/");
			URLConnection conn = url.openConnection();

			conn.setDoOutput(true);

			OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

			writer.write(urlParameters);
			writer.flush();

			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

			PrintWriter out = new PrintWriter(filePathHardness);
			while ((line = reader.readLine()) != null) {
				//System.out.println(line);
				out.println(line);
			}
			writer.close();
			reader.close();
			out.close();
			
			// Move
			File afile = new File(filePathHardness);
			afile.renameTo(new File(filePath));
		} catch (Exception e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
	

	private static void importarVendedores() {
		String filePath = "C:\\sgloja\\importacao\\vendedores.txt";
		File f = new File(filePath);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		String filePathHardness = "C:\\hardness\\vendedores.txt";
		f = new File(filePathHardness);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		try {
			String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63";
			URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/exportarVendedores/");
			//URL url = new URL("http://andrey/outros/integracaoPDV/integracaoSG/exportarVendedores/");
			URLConnection conn = url.openConnection();

			conn.setDoOutput(true);

			OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

			writer.write(urlParameters);
			writer.flush();

			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

			PrintWriter out = new PrintWriter(filePathHardness);
			while ((line = reader.readLine()) != null) {
				//System.out.println(line);
				out.println(line);
			}
			writer.close();
			reader.close();
			out.close();
			
			// Move
			File afile = new File(filePathHardness);
			afile.renameTo(new File(filePath));
		} catch (Exception e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
	
	private static void importarPedidos() {
		String filePath = "C:\\sgloja\\importacao\\pedidos.txt";
		File f = new File(filePath);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		String filePathHardness = "C:\\hardness\\pedidos.txt";
		f = new File(filePathHardness);
		if(f.exists() && !f.isDirectory()) { 
			f.delete();
		}
		try {
			String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63";
			URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/exportarPedidos/");
			URLConnection conn = url.openConnection();

			conn.setDoOutput(true);

			OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

			writer.write(urlParameters);
			writer.flush();

			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));
			
			Boolean achou = false;
			PrintWriter out = new PrintWriter(filePathHardness);
			while ((line = reader.readLine()) != null) {
				achou = true;
				//System.out.println("-");
				//System.out.println(line);
				out.println(line);
			}
			writer.close();
			reader.close();
			out.close();
			
			// Move
			if (achou) {
				File afile = new File(filePathHardness);
				afile.renameTo(new File(filePath));
			} else {
				f = new File(filePathHardness);
				if(f.exists() && !f.isDirectory()) { 
					f.delete();
				}
			}
		} catch (Exception e2) {
			// TODO Auto-generated catch block
			e2.printStackTrace();
		}
	}
	
	private static void enviarPDV() throws IOException {
		Files.walk(Paths.get("C:\\sgloja\\exportacao")).forEach(filePath -> {
		    if (Files.isRegularFile(filePath)) {
		        System.out.println(filePath);
		        
				try {
					String urlParameters = "ajax=true&noEmp=true&p=" + empresaAtual + "&u=63&filename=" + filePath + "&content=" + PDV.fileToString(filePath.toString());
					URL url = new URL(urlEmpresa + "/outros/integracaoPDV/integracaoSG/importarCupomFiscal/");
					URLConnection conn = url.openConnection();

					conn.setDoOutput(true);

					OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());

					writer.write(urlParameters);
					writer.flush();

					String line;
					BufferedReader reader = new BufferedReader(new InputStreamReader(conn.getInputStream()));

					while ((line = reader.readLine()) != null) {
						if (line.equals("true")) {
							Files.delete(filePath);
						}
						System.out.println(line);
					}
					writer.close();
					reader.close();
				} catch (Exception e2) {
					// TODO Auto-generated catch block
					e2.printStackTrace();
				}
		        
		    }
		});
	}
	
	private static String fileToString(String filename) throws IOException
	{
	    @SuppressWarnings("resource")
		BufferedReader reader = new BufferedReader(new FileReader(filename));
	    StringBuilder builder = new StringBuilder();
	    String line;    

	    // For every line in the file, append it to the string builder
	    while((line = reader.readLine()) != null)
	    {
	        builder.append(line);
	        builder.append(System.getProperty("line.separator"));
	    }
	    reader.close();
	    return builder.toString();
	}

}
