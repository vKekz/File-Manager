import { Component, Input } from "@angular/core";

@Component({
  selector: "app-button",
  imports: [],
  templateUrl: "./button.component.html",
  styleUrl: "./button.component.css",
})
export class ButtonComponent {
  @Input()
  public text: string = "";

  @Input()
  public padding: number = 12;
}
