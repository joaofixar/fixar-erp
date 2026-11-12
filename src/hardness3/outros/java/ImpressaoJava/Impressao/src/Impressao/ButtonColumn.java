package Impressao;

import java.awt.Component;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import javax.swing.AbstractCellEditor;
import javax.swing.JButton;
import javax.swing.JTable;
import javax.swing.UIManager;
import javax.swing.table.*;

class ButtonColumn extends AbstractCellEditor implements TableCellRenderer, TableCellEditor, ActionListener {
	private static final long serialVersionUID = 1L;
	JTable table;
	JButton renderButton;
	JButton editButton;
	String text;

	public ButtonColumn(JTable table, int column) {
		// Super vai chamar a construtor da classe pai, ou seja o construtor de
		// TableCellRenderer, TableCellEditor e ActionListener
		super();
		this.table = table;
		renderButton = new JButton("");
		editButton = new JButton();
		editButton.setFocusPainted(false);
		editButton.addActionListener(this);
		TableColumnModel columnModel = table.getColumnModel();
		columnModel.getColumn(column).setCellRenderer(this);
		columnModel.getColumn(column).setCellEditor(this);
	}

	public Component getTableCellRendererComponent(JTable table, Object value, boolean isSelected, boolean hasFocus,
			int row, int column) {
		if (hasFocus) {
			renderButton.setForeground(table.getForeground());
			renderButton.setBackground(UIManager.getColor("Button.background"));
		} else if (isSelected) {
			renderButton.setForeground(table.getSelectionForeground());
			renderButton.setBackground(table.getSelectionBackground());
		} else {
			renderButton.setForeground(table.getForeground());
			renderButton.setBackground(UIManager.getColor("Button.background"));
		}
		renderButton.setText((value == null) ? " " : value.toString());
		return renderButton;
	}

	public Component getTableCellEditorComponent(JTable table, Object value, boolean isSelected, int row, int column) {
		text = (value == null) ? "" : value.toString();
		// Seta o nome do botão depois de clicar nele
		editButton.setText(text);
		return editButton;
	}

	// Quando eu clico no botão o que acontece
	public Object getCellEditorValue() {
		return text;
	}

	public void actionPerformed(ActionEvent e) {
		String nomeArquivo = (String) table.getValueAt(table.getSelectedRow(), 0);
		if (table.getSelectedColumn() == 1) {
			Impressao.imprimirEtiqueta(Impressao.nomeImpressora, nomeArquivo);
		} else if (table.getSelectedColumn() == 2) {
			File file = new File("C:\\hardness\\impressao\\" + nomeArquivo);
			file.delete();
		}
		try {
			Impressao.RemoverTela();
		} catch (Exception e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
	}

}
