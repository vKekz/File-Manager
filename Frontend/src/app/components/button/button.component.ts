import { Component, Input } from "@angular/core";

@Component({
  selector: "app-button",
  imports: [],
  templateUrl: "./button.component.html",
  styleUrl: "./button.component.css",
})
export class ButtonComponent {
  @Input({ required: true })
  public text: string = "";

  @Input()
  public padding: number = 12;
}
