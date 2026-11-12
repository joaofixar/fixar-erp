package Impressao;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Dimension;
import java.awt.EventQueue;
import java.awt.List;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;

import javax.swing.AbstractCellEditor;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.UIManager;
import javax.swing.border.EmptyBorder;
import javax.swing.table.TableCellEditor;
import javax.swing.table.TableCellRenderer;
import javax.swing.table.TableColumnModel;
import javax.swing.SwingConstants;
import javax.swing.JLabel;
import java.awt.Color;
import java.awt.TextField;

public class TelaImpressao extends JFrame {

	private JPanel contentPane;

	/**
	 * Create the frame.
	 * 
	 * @throws Exception
	 */
	public TelaImpressao() throws Exception {
		setAlwaysOnTop(true);
		setResizable(false);

		// diret�rio que ser� listado.
		File baseFolder = new File("C:\\hardness\\impressao\\");
		if(!baseFolder.exists()) {
			baseFolder.mkdirs();
		}
		setTitle("HARDNESS SISTEMAS - FILA DE IMPRESSÃO - Versão 1.0.1");
		
		// Listar os arquivos das etiquetas
		Object[][] dados = Impressao.listarEtiquetas(baseFolder);

		// FIM BUSCANDO ARQUIVOS
		setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
		setBounds(450, 450, 700, 400);
		contentPane = new JPanel();
		
		contentPane.setBorder(new EmptyBorder(5, 5, 5, 5));
		contentPane.setLayout(new BorderLayout(0, 0));
		setContentPane(contentPane);
		
//		buttonAtualizar.setBorder(new EmptyBorder(5, 5, 5, 5));
//		buttonAtualizar.setLayout(new BorderLayout(0, 0));
//		setContentPane(buttonAtualizar);

		String[] colunas = new String[] { "Impressões", "", "" };

		JTable tabela = new JTable(dados, colunas);
		
		JScrollPane scroll = new JScrollPane();
		scroll.setViewportView(tabela);
		getContentPane().add(scroll, BorderLayout.CENTER);

		ButtonColumn botaoImpressao = new ButtonColumn(tabela, 1);
		ButtonColumn botaoExcluir = new ButtonColumn(tabela, 2);
		
		tabela.getColumnModel().getColumn(0).setPreferredWidth(500);
		tabela.getColumnModel().getColumn(1).setPreferredWidth(100);
		tabela.getColumnModel().getColumn(2).setPreferredWidth(100);
		tabela.setRowHeight(24); 
		
		JButton btnAtualizar = new JButton("Atualizar");

		
		contentPane.add(btnAtualizar, BorderLayout.NORTH);
		btnAtualizar.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent e) {
				try {
					Impressao.downloadEtiqueta();
					Impressao.RemoverTela();
				} catch (Exception e1) {
					// TODO Auto-generated catch block
					e1.printStackTrace();
				}
			}
		});
	}
}
